<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BgpData
 */
class BgpData
{
    /**
     * @var integer
     */
    private $fileTime;

    /**
     * @var string
     */
    private $webSize;

    /**
     * @var \DateTime
     */
    private $ts;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector
     */
    private $collector;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    private $bgpType;

    /**
     * Get fileTime
     *
     * @return integer
     */
    public function getFileTime()
    {
        return $this->fileTime;
    }

    /**
     * Get webSize
     *
     * @return string
     */
    public function getWebSize()
    {
        return $this->webSize;
    }

    /**
     * Get ts
     *
     * @return \DateTime
     */
    public function getTs()
    {
        return $this->ts;
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
