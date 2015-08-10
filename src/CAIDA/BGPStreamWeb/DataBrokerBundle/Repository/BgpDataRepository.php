<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\Interval;
use Doctrine\ORM\EntityRepository;

class BgpDataRepository extends EntityRepository {

    /**
     * @param Interval $interval
     * @param null $projects
     * @param null $collectors
     * @param null $types
     * @return array
     */
    public function findByIntervalProjectsCollectorsTypes($interval,
                                                          $projects=null,
                                                          $collectors=null,
                                                          $types=null)
    {
        if (!$interval) {
            throw new \InvalidArgumentException('Missing start or end time');
        }
        $queryStr =
            'SELECT d FROM CAIDABGPStreamWebDataBrokerBundle:BgpData d ';

        // TODO: add the duration of the file to detect the end
        $queryStr .=
            'WHERE d.fileTime >= :starttime AND d.fileTime <= :endtime ';
        $queryStr .= 'ORDER BY d.fileTime ASC';

        $query = $this->getEntityManager()
                      ->createQuery($queryStr);

        $query->setParameter('starttime', $interval->getStart());
        $query->setParameter('endtime', $interval->getEnd());

        return $query->getResult();
    }

}
