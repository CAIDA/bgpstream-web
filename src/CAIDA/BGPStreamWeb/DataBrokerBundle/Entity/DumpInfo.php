<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DumpInfo
 */
class DumpInfo
{
    /**
     * @var integer
     */
    private $collectorTypeId;

    /**
     * @var integer
     */
    private $period;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType
     */
    private $collectorType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = new \Doctrine\Common\Collections\ArrayCollection();
        $this->collectors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bgpTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get collectorTypeId
     *
     * @return integer
     */
    public
    function getCollectorTypeId()
    {
        return $this->collectorTypeId;
    }

    /**
     * Get period
     *
     * @return integer
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Get collectorType
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType
     */
    public function getCollectorType()
    {
        return $this->collectorType;
    }

    /**
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get collectors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * Get bgpTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBgpTypes()
    {
        return $this->bgpTypes;
    }
}
