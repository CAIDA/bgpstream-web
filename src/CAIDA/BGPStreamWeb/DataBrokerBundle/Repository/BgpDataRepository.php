<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BgpDataRepository extends EntityRepository {

    public function findByIntervalProjectsCollectorsTypes($startTime,
                                                          $endTime,
                                                          $projects=null,
                                                          $collectors=null,
                                                          $types=null)
    {
        if (!$startTime || !$endTime) {
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

        $query->setParameter('starttime', $startTime);
        $query->setParameter('endtime', $endTime);

        return $query->getResult();
    }

}
