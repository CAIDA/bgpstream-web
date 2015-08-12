<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\Interval;
use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\IntervalSet;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use Doctrine\ORM\EntityRepository;

class BgpDataRepository extends EntityRepository {

    // max window of time to ask from the DB
    const QUERY_WINDOW = 7200;

    // fudge time to allow for file times that are slightly wrong
    const FILE_TIME_OFFSET = 120;

    // fudge time in case some of the records for the interval(s) we want are
    // stored in files outside the interval
    const START_OFFSET = 1020; // 900 + 120

    const OUT_OF_ORDER_WINDOW = 86400;// 24 * 3600

    private function buildIntervalWhere($interval, &$parameters, &$cnt, $applyOffset=true)
    {
        $p1 = $cnt++;
        $p2 = $cnt++;
        $parameters['w'.$p1] =
            $applyOffset ? $interval->getStart() - static::START_OFFSET :
                $interval->getStart();
        $parameters['w'.$p2] = $interval->getEnd();

        return '(d.fileTime >= :w' . $p1 . ' AND d.fileTime <= :w' . $p2 . ')';
    }

    /**
     * @param IntervalSet $intervals
     * @param $minInitialTime
     * @param $parameters
     *
     * @return string
     */
    private function buildFileTimeWhere($intervals, $minInitialTime, &$parameters)
    {
        $where = '';

        // compute our constraint interval
        $constraintInterval = new Interval(
            $minInitialTime,
            $minInitialTime + static::QUERY_WINDOW
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
        $parameters['w3'] = $dataAddedSince;
        $parameters['w4'] = $responseTime;

        // look into the already-processed data for files that have been added
        // since we last looked
        return 'd.fileTime < :w1 AND d.fileTime > :w2 AND d.ts > :w3 AND d.ts < :w4';
    }

    private function findDataByWhere($projects, $collectors, $types, $whereStr, $whereParams)
    {
        $parameters = [];
        $qb         = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
           ->from('CAIDABGPStreamWebDataBrokerBundle:BgpData', 'd');
        //->orderBy('d.fileTime, d.bgpType', 'ASC'); // we sort ourselves

        // needed by both project and collector.
        // i'm pretty sure there is no cost if it is not used
        $qb->join('d.collector', 'coll');

        // filter by project
        if($projects && count($projects)) {
            $qb->join('coll.project', 'proj');
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
            $qb->join('d.bgpType', 'type');
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

        // set the correct minInitialTime and minInitialQueryTime
        $minInitialQueryTime = $minInitialTime;
        if(!$minInitialTime) {
            // set to the first interval
            $minInitialTime = $intervals->getFirstInterval()->getStart();
            $minInitialQueryTime = $minInitialTime - static::START_OFFSET;
        } elseif(!$intervals->getIntervalOVerlapping($minInitialTime)) {
            // the time they asked for is not in an interval, bump to the next
            //interval start
            $nextInterval = $intervals->getNextInterval($minInitialTime);
            if(!$nextInterval) {
                // there is no more data...
                return [];
            }
            $minInitialTime = $nextInterval->getStart();
            $minInitialQueryTime = $minInitialTime - static::START_OFFSET;
        }

        // build the fileTime where clause
        $timeParams = [];
        $timeWhere =
            $this->buildFileTimeWhere($intervals, $minInitialQueryTime,
                                      $timeParams);
        if (!$timeWhere) {
            return [];
        }
        $newFiles = $this->findDataByWhere($projects, $collectors, $types, $timeWhere, $timeParams);

        // build the ts where clause
        $oooFiles = [];
        if ($dataAddedSince) {
            $tsParams = [];
            $tsWhere =
                $this->buildTsWhere(
                    $responseTime,
                    $dataAddedSince,
                    $minInitialQueryTime,
                    $tsParams
                );
            if(!$tsWhere) {
                return [];
            }
            $oooFiles = $this->findDataByWhere($projects, $collectors, $types, $tsWhere, $tsParams);
        }

        $files = array_merge($newFiles, $oooFiles);
        usort($files, ['CAIDA\BGPStreamWeb\DataBrokerBundle\Repository\BgpDataRepository', 'cmpBgpData']);

        // filter the results to remove files that we accidentally got due to
        // our overzealous (but fast!) START_OFFSET
        $filtered = [];
        /* @var Interval $overlapInterval */
        $overlapInterval = null;
        foreach ($files as $file) {
            /* @var BgpData $file */
            if(($file->getFileTime() + $file->getDumpInfo()->getDuration() +
                static::FILE_TIME_OFFSET) >= $minInitialTime
            ) {
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
                    // skip this file
                    continue;
                }
                // keep this file
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

}
