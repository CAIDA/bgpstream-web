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
use Doctrine\ORM\EntityRepository;

class BgpDataRepository extends EntityRepository {

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
            ->orderBy('d.fileTime', 'ASC');

        // TODO: add the duration of the file to detect the end
        $parameters = [];
        $cnt = 0;
        $where = '';
        foreach ($intervals->getIntervals() as $interval) {
            if ($cnt > 0) {
                $where .= ' OR ';
            }
            $where .= '(d.fileTime >= ?'.$cnt++ .' AND d.fileTime <= ?'.$cnt++.')';
            $parameters[] = $interval->getStart();
            $parameters[] = $interval->getEnd();
        }
        $qb->andWhere($where);

        $qb->andWhere('d.fileTime >= ?'.$cnt++.' AND d.fileTime <= ?'.$cnt);
        $parameters[] = $constraintInterval->getStart();
        $parameters[] = $constraintInterval->getEnd();

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }

}
