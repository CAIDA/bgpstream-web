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
     * Set fileTime
     *
     * @param integer $fileTime
     * @return BgpData
     */
    public function setFileTime($fileTime)
    {
        $this->fileTime = $fileTime;

        return $this;
    }

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
     * Set webSize
     *
     * @param string $webSize
     * @return BgpData
     */
    public function setWebSize($webSize)
    {
        $this->webSize = $webSize;

        return $this;
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
     * Set ts
     *
     * @param \DateTime $ts
     * @return BgpData
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
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
     * Set collector
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector $collector
     * @return BgpData
     */
    public function setCollector(\CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector $collector = null)
    {
        $this->collector = $collector;

        return $this;
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
     * Set bgpType
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpType
     * @return BgpData
     */
    public function setBgpType(\CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpType = null)
    {
        $this->bgpType = $bgpType;

        return $this;
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
