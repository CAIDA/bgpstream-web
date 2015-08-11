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

    // fudge time to allow for file times that are slightly wrong
    const FILE_TIME_OFFSET = 120;

    // fudge time in case some of the records for the interval(s) we want are
    // stored in files outside the interval
    const START_OFFSET = 1020; // 900 + 120

    private function buildIntervalWhere($interval, &$parameters, &$cnt)
    {
        $parameters[] = $interval->getStart() - static::START_OFFSET;
        $parameters[] = $interval->getEnd();

        return '(d.fileTime >= ?' . $cnt++ . ' AND d.fileTime <= ?' . $cnt++ . ')';
    }

    /**
     * @param IntervalSet $intervals
     * @param Interval $constraintInterval
     * @param null $projects
     * @param null $collectors
     * @param null $types
     * @return array
     */
    public function findByIntervalProjectsCollectorsTypes($intervals,
                                                          $constraintInterval,
                                                          $projects=null,
                                                          $collectors=null,
                                                          $types=null)
    {
        if (!$intervals || !count($intervals) || !$constraintInterval) {
            throw new \InvalidArgumentException('Missing intervals');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
            ->from('CAIDABGPStreamWebDataBrokerBundle:BgpData', 'd')
            ->orderBy('d.fileTime, d.bgpType', 'ASC');

        $parameters = [];
        $cnt = 0;

        // build the where query for the user's intervals
        $where = '';
        foreach ($intervals->getIntervals() as $interval) {
            if ($cnt > 0) {
                $where .= ' OR ';
            }
            $where .= $this->buildIntervalWhere($interval, $parameters, $cnt);
        }
        // all the intervals the user is interested in
        $qb->andWhere($where);

        // add our constraint interval
        $qb->andWhere(
            $this->buildIntervalWhere($constraintInterval, $parameters, $cnt)
        );

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
                static::FILE_TIME_OFFSET) >= $constraintInterval->getStart()
            ) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

}
