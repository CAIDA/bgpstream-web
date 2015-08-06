<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CollectorRepository extends EntityRepository
{

    public
    function findByName($name)
    {
        $queryStr =
            'SELECT c FROM CAIDABGPStreamWebDataBrokerBundle:Collector c ';
        if($name) {
            $name = str_replace('*', '%', $name);
            $queryStr .= 'WHERE c.name LIKE :name ';
        }
        $queryStr .= 'ORDER BY c.name ASC';

        $query = $this->getEntityManager()
                      ->createQuery($queryStr);

        if($name) {
            $query->setParameter('name', $name);
        }

        return $query->getResult();
    }

}
