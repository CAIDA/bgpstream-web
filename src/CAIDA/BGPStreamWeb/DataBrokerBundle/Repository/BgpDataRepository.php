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
        $parameters[] =
            $applyOffset ? $interval->getStart() - static::START_OFFSET :
                $interval->getStart();
        $parameters[] = $interval->getEnd();

        return '(d.fileTime >= ?' . $cnt++ . ' AND d.fileTime <= ?' . $cnt++ . ')';
    }

    /**
     * @param IntervalSet $intervals
     * @param $minInitialTime
     * @param $parameters
     * @param $cnt
     *
     * @return string
     */
    private function buildFileTimeWhere($intervals, $minInitialTime, &$parameters, &$cnt)
    {
        $where = '';

        // compute our constraint interval
        $constraintInterval = new Interval(
            $minInitialTime,
            $minInitialTime + static::QUERY_WINDOW
        );

        // build the where query for the user's intervals
        $userWhere = '';
        foreach($intervals->getIntervals() as $interval) {
            if($cnt > 0) {
                $userWhere .= ' OR ';
            }
            $userWhere .= $this->buildIntervalWhere($interval, $parameters, $cnt);
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

    private function buildTsWhere($dataAddedSince, $minInitialTime, &$parameters, &$cnt)
    {
        $parameters[] = $minInitialTime;
        $parameters[] = $minInitialTime-static::OUT_OF_ORDER_WINDOW;
        $parameters[] = $dataAddedSince;

        // look into the already-processed data for files that have been added
        // since we last looked
        return 'd.fileTime < ?' . $cnt++ . ' AND d.fileTime > ?' . $cnt++ .
               ' AND d.ts > ?' . $cnt++ . ' AND d.ts < CURRENT_TIMESTAMP()-1';
    }

    /**
     * @param IntervalSet $intervals
     * @param integer $minInitialTime
     * @param integer $lastResponseId
     * @param null $projects
     * @param null $collectors
     * @param null $types
     * @return array
     */
    public function findByIntervalProjectsCollectorsTypes($intervals,
                                                          $minInitialTime=null,
                                                          $dataAddedSince=null,
                                                          $projects=null,
                                                          $collectors=null,
                                                          $types=null)
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

        $parameters = [];
        $cnt        = 0;
        $qb         = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
           ->from('CAIDABGPStreamWebDataBrokerBundle:BgpData', 'd')
           ->orderBy('d.fileTime, d.bgpType', 'ASC');

        // build the fileTime where clause
        $fileTimeWhere =
            $this->buildFileTimeWhere($intervals, $minInitialQueryTime,
                                      $parameters, $cnt);
        if (!$fileTimeWhere) {
            return [];
        }
        $qb->andWhere($fileTimeWhere);

        // build the ts where clause
        if ($dataAddedSince) {
            $tsWhere =
                $this->buildTsWhere($dataAddedSince, $minInitialQueryTime,
                                    $parameters, $cnt);
            if(!$tsWhere) {
                return [];
            }
            $qb->andWhere($tsWhere);
        }

        // needed by both project and collector.
        // i'm pretty sure there is no cost if it is not used
        $qb->join('d.collector', 'coll');

        // filter by project
        if ($projects && count($projects)) {
            $qb->join('coll.project', 'proj');
            $qb->andWhere('proj.name IN (?'.$cnt++.')');
            $parameters[] = $projects;
        }

        // filter by collector
        if ($collectors && count($collectors)) {
            $qb->andWhere('coll.name IN (?'.$cnt++.')');
            $parameters[] = $collectors;
        }

        // filter by type
        if ($types && count($types)) {
            $qb->join('d.bgpType', 'type');
            $qb->andWhere('type.name IN (?'.$cnt++.')');
            $parameters[] = $types;
        }

        $qb->setParameters($parameters);

        $files = $qb->getQuery()->getResult();

        // filter the results to remove files that we accidentally got due to
        // our overzealous (but fast!) START_OFFSET
        $filtered = [];
        foreach ($files as $file) {
            /* @var BgpData $file */
            if(($file->getFileTime() + $file->getDumpInfo()->getDuration() +
                static::FILE_TIME_OFFSET) >= $minInitialTime
            ) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

}
