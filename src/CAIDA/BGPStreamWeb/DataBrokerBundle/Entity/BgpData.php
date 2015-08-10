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
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType
     */
    private $collectorType;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector
     */
    private $collector;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    private $bgpType;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\DumpInfo
     */
    private $dumpInfo;

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
     * Get collectorType
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType
     */
    public function getCollectorType()
    {
        return $this->collectorType;
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

    /**
     * Get dumpInfo
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\DumpInfo
     */
    public function getDumpInfo()
    {
        return $this->dumpInfo;
    }
}
