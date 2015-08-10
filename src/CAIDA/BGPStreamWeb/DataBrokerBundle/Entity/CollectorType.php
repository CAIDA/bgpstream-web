<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CollectorType
 */
class CollectorType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector
     */
    private $collector;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    private $bgpType;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get collector
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * Get bgpType
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    public function getBgpType()
    {
        return $this->bgpType;
    }
}
