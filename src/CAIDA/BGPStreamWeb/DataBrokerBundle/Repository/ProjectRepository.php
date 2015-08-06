<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/6/15
 * Time: 11:45 AM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository {

    public function findByName($name)
    {
        $queryStr =
            'SELECT p FROM CAIDABGPStreamWebDataBrokerBundle:Project p ';
        if($name) {
            $name = str_replace('*', '%', $name);
            $queryStr .= 'WHERE p.name LIKE :name ';
        }
        $queryStr .= 'ORDER BY p.name ASC';

        $query = $this->getEntityManager()
                      ->createQuery($queryStr);

        if($name) {
            $query->setParameter('name', $name);
        }

        return $query->getResult();
    }

}
