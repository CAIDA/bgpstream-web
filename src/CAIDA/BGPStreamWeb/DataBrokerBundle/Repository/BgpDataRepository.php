<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use CAIDA\BGPStreamWeb\DataBrokerBundle\Interval\Interval;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Interval\IntervalSet;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use Doctrine\ORM\EntityRepository;

class BgpDataRepository extends EntityRepository {

    // max window of time to ask from the DB
    const QUERY_WINDOW = 7200; // 2 hours

    // max query window length when doing exponential expansion
    const QUERY_WINDOW_MAX = 2419200; // 28 days

    // fudge time to allow for file times that are slightly wrong
    const FILE_TIME_OFFSET = 120;

    // fudge time in case some of the records for the interval(s) we want are
    // stored in files outside the interval
    const START_OFFSET = 1020; // 900 + 120

    const OUT_OF_ORDER_WINDOW = 86400;// 24 * 3600

    /**
     * @param Interval $interval
     * @param      $parameters
     * @param      $cnt
     * @param bool $applyOffset
     *
     * @return string
     */
    private function buildIntervalWhere($interval, &$parameters, &$cnt, $applyOffset=true)
    {
        $p1 = $cnt++;
        $p2 = $cnt++;
        $parameters['w'.$p1] =
            $applyOffset ? $interval->getStart() - static::START_OFFSET :
                $interval->getStart();
        if ($interval->getEnd() != Interval::FOREVER) {
            $parameters['w' . $p2] = $interval->getEnd();

            return '(d.fileTime >= :w' . $p1 . ' AND d.fileTime <= :w' . $p2 . ')';
        } else {
            return '(d.fileTime >= :w' . $p1 . ')';
        }
    }

    /**
     * @param IntervalSet $intervals
     * @param $minInitialTime
     * @param $parameters
     * @param $retryCnt
     * @param $responseTime
     *
     * @return string
     */
    private function buildFileTimeWhere($intervals, $minInitialTime, &$parameters, $retryCnt, $responseTime)
    {
        $where = '';

        // compute constraint interval length based on number of retries
        $constraintLength = ($retryCnt+1) *
                            min(static::QUERY_WINDOW_MAX,
                                static::QUERY_WINDOW * pow(2, $retryCnt));

        // if we've already tried to get data and the end of the constraint
        // interval is after the end of the last interval in the set, return null
        if($retryCnt > 0 &&
           // end of constraint window is in the "future"
           (($minInitialTime + $constraintLength) > $responseTime ||
            // or the end of the constraint window is after the end of the overall interval
            ($intervals->getLastInterval()->getEnd() != Interval::FOREVER &&
             ($minInitialTime + $constraintLength) >
             $intervals->getLastInterval()->getEnd()))
        ) {
            return null;
        }

        // compute our constraint interval
        $constraintInterval = new Interval(
            $minInitialTime,
            $minInitialTime + $constraintLength
        );

        // build the where query for the user's intervals
        $userWhere = '';
        $cnt = 0;
        foreach($intervals->getIntervals() as $interval) {
            if($cnt > 0) {
                $userWhere .= ' OR ';
            }
            $userWhere .= $this->buildIntervalWhere($interval, $parameters, $cnt);
            $cnt++;
        }
        // all the intervals the user is interested in
        $where .= '('.$userWhere.')';

        // add our constraint interval
        $where .= ' AND ';
        // set applyOffset to false because the constraint interval has the
        // offset applied (if needed) already
        $where .= $this->buildIntervalWhere($constraintInterval, $parameters,
                                            $cnt,
                                            false);

        return $where;
    }

    private function buildTsWhere($responseTime, $dataAddedSince, $minInitialTime, &$parameters)
    {
        $parameters['w1'] = $minInitialTime;
        $parameters['w2'] = $minInitialTime-static::OUT_OF_ORDER_WINDOW;
        $dAS =  new \DateTime();
        $dAS->setTimestamp($dataAddedSince);
        $parameters['w3'] = $dAS;
        $rt = new \DateTime();
        $rt->setTimestamp($responseTime);
        $parameters['w4'] = $rt;

        // look into the already-processed data for files that have been added
        // since we last looked
        return 'd.fileTime < :w1 AND d.fileTime > :w2 AND d.ts >= :w3 AND d.ts < :w4';
    }

    private function findDataByWhere($projects, $collectors, $types, $whereStr, $whereParams)
    {
        $parameters = [];
        $qb         = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
           ->from('CAIDABGPStreamWebDataBrokerBundle:BgpData', 'd');
        //->orderBy('d.fileTime, d.bgpType', 'ASC'); // we sort ourselves

        $qb->leftJoin('d.collectorType', 'collType');
        $qb->leftJoin('d.dumpInfo', 'dumpInfo');
        $qb->leftJoin('collType.collector', 'coll');
        $qb->leftJoin('coll.project', 'proj');
        $qb->leftJoin('collType.bgpType', 'type');

        // filter by project
        if($projects && count($projects)) {
            $qb->andWhere('proj.name IN (:projs)');
            $parameters['projs'] = $projects;
        }

        // filter by collector
        if($collectors && count($collectors)) {
            $qb->andWhere('coll.name IN (:colls)');
            $parameters['colls'] = $collectors;
        }

        // filter by type
        if($types && count($types)) {
            $qb->andWhere('type.name IN (:types)');
            $parameters['types'] = $types;
        }

        $qb->andWhere($whereStr);
        $parameters = array_merge($parameters, $whereParams);

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param BgpData $a
     * @param BgpData $b
     * @return boolean
     */
    private function cmpBgpData($a, $b)
    {
        if ($a->getFileTime() == $b->getFileTime()) {
            return $a->getBgpType()->getId() - $b->getBgpType()->getId();
        }
        return $a->getFileTime() - $b->getFileTime();
    }

    /**
     * @param integer $responseTime
     * @param IntervalSet $intervals
     * @param integer $minInitialTime
     * @param integer $dataAddedSince
     * @param null $projects
     * @param null $collectors
     * @param null $types
     * @return array
     */
    public function findByIntervalProjectsCollectorsTypes(
        $responseTime,
        $intervals,
        $minInitialTime=null,
        $dataAddedSince=null,
        $projects=null,
        $collectors=null,
        $types=null
    )
    {
        if (!$intervals || !count($intervals)) {
            throw new \InvalidArgumentException('Missing intervals');
        }

        // set mysql to UTC
        $this->getEntityManager()->getConnection()
             ->prepare("SET time_zone='+0:0'")->execute();

        // if there is no data, retry, expanding our constraint window until
        // either we find data, or the constraint window extends past the end
        // of the last interval
        $files = [];
        $retries = 0;
        while(!count($files)) {

            // set the correct minInitialTime and minInitialQueryTime
            $minInitialQueryTime = $minInitialTime;
            if(!$minInitialTime) {
                // set to the first interval
                $minInitialTime      =
                    $intervals->getFirstInterval()->getStart();
                $minInitialQueryTime = $minInitialTime - static::START_OFFSET;
            } elseif(!$intervals->getIntervalOverlapping($minInitialTime)) {
                // the time they asked for is not in an interval, bump to the next
                //interval start
                $nextInterval = $intervals->getNextInterval($minInitialTime);
                if(!$nextInterval) {
                    // there is no more data...
                    return [];
                }
                $minInitialTime      = $nextInterval->getStart();
                $minInitialQueryTime = $minInitialTime - static::START_OFFSET;
            }

            // build the fileTime where clause
            $timeParams = [];
            $timeWhere  =
                $this->buildFileTimeWhere($intervals, $minInitialQueryTime,
                                          $timeParams, $retries++, $responseTime);
            if(!$timeWhere) { // there's no data!
                return [];
            }
            $newFiles = $this->findDataByWhere($projects, $collectors, $types,
                                               $timeWhere, $timeParams);


            // filter the results to remove files that we accidentally got due to
            // our overzealous (but fast!) START_OFFSET
            // also filter any files added in the current second to avoid a race condition
            $filteredNewFiles = [];
            foreach($newFiles as $file) {
                /* @var BgpData $file */
                if($file->getTs()->getTimestamp() < $responseTime &&
                   ($file->getFileTime() + $file->getDumpInfo()->getDuration() +
                    static::FILE_TIME_OFFSET) >= $minInitialTime
                ) {
                    $filteredNewFiles[] = $file;
                }
            }
            $newFiles = $filteredNewFiles;

            // build the ts where clause
            $oooFiles = [];
            if($dataAddedSince) {
                $tsParams = [];
                $tsWhere  =
                    $this->buildTsWhere(
                        $responseTime,
                        $dataAddedSince,
                        $minInitialQueryTime,
                        $tsParams
                    );
                if(!$tsWhere) {
                    return [];
                }
                $oooFiles =
                    $this->findDataByWhere($projects, $collectors, $types,
                                           $tsWhere, $tsParams);
            }

            $files = array_merge($newFiles, $oooFiles);
        }

        usort($files, ['CAIDA\BGPStreamWeb\DataBrokerBundle\Repository\BgpDataRepository', 'cmpBgpData']);

        $filtered = [];
        /* @var Interval $overlapInterval */
        $overlapInterval = null;
        $overlapSet = [];
        foreach ($files as $file) {
            /* @var BgpData $file */
            // does this file overlap with our interval?
            $ti = new Interval(
            // ribs span [ts-120, ts+120]
                $file->getBgpType()->getName() == "ribs" ?
                    $file->getFileTime() -
                    $file->getDumpInfo()->getDuration() :
                    $file->getFileTime(),
                $file->getFileTime() +
                $file->getDumpInfo()->getDuration()
            );
            if(!$overlapInterval) {
                $overlapInterval = $ti;
            } else if(!$overlapInterval->extendOverlapping($ti)) {
                // new set

                // first, add the previous overlap set (ensures we only return complete sets)
                if(count($overlapSet)) {
                    $filtered = array_merge($filtered, $overlapSet);
                    $overlapSet = [];
                }

                $overlapInterval = $ti;
            }
            // add this file to the set
            $overlapSet[] = $file;
        }
        // if all our files form one set, then return that
        if (!count($filtered)) {
            return $overlapSet;
        } else {
            return $filtered;
        }
    }

    /**
     * @return array
     */
    public
    function findTimeRanges()
    {
        $queryStr =
            'SELECT d.collectorTypeId, MIN(d.fileTime) as oldestDumpTime, MAX(d.fileTime) as latestDumpTime ' .
            'FROM CAIDABGPStreamWebDataBrokerBundle:BgpData d ' .
            'GROUP BY d.collectorTypeId';

        $query = $this->getEntityManager()
                      ->createQuery($queryStr);

        return $query->getResult();
    }

}
