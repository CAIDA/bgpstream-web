<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType;
use Doctrine\ORM\EntityRepository;

class CollectorTypeRepository extends EntityRepository
{

    /**
     * @param CollectorType $collectorType
     *
     * @return array
     */
    public
    function findTimeRange($collectorType)
    {
        $queryStr =
            'SELECT MIN(d.fileTime) as oldestDumpTime, MAX(d.fileTime) as latestDumpTime '.
            'FROM CAIDABGPStreamWebDataBrokerBundle:BgpData d '.
            'JOIN d.collectorType ct WHERE ct.id = :ctid';

        $query = $this->getEntityManager()
                      ->createQuery($queryStr);

        $query->setParameter('ctid', $collectorType->getId());

        return $query->getSingleResult();
    }

}
