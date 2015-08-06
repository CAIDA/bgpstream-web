<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileStat
 */
class FileStat
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $fileTime;

    /**
     * @var integer
     */
    private $lastIndex;

    /**
     * @var integer
     */
    private $downloadStart;

    /**
     * @var integer
     */
    private $downloadStop;

    /**
     * @var integer
     */
    private $inDb;

    /**
     * @var \DateTime
     */
    private $ts;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project
     */
    private $project;

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
     * Set fileTime
     *
     * @param integer $fileTime
     * @return FileStat
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
     * Set lastIndex
     *
     * @param integer $lastIndex
     * @return FileStat
     */
    public function setLastIndex($lastIndex)
    {
        $this->lastIndex = $lastIndex;

        return $this;
    }

    /**
     * Get lastIndex
     *
     * @return integer 
     */
    public function getLastIndex()
    {
        return $this->lastIndex;
    }

    /**
     * Set downloadStart
     *
     * @param integer $downloadStart
     * @return FileStat
     */
    public function setDownloadStart($downloadStart)
    {
        $this->downloadStart = $downloadStart;

        return $this;
    }

    /**
     * Get downloadStart
     *
     * @return integer 
     */
    public function getDownloadStart()
    {
        return $this->downloadStart;
    }

    /**
     * Set downloadStop
     *
     * @param integer $downloadStop
     * @return FileStat
     */
    public function setDownloadStop($downloadStop)
    {
        $this->downloadStop = $downloadStop;

        return $this;
    }

    /**
     * Get downloadStop
     *
     * @return integer 
     */
    public function getDownloadStop()
    {
        return $this->downloadStop;
    }

    /**
     * Set inDb
     *
     * @param integer $inDb
     * @return FileStat
     */
    public function setInDb($inDb)
    {
        $this->inDb = $inDb;

        return $this;
    }

    /**
     * Get inDb
     *
     * @return integer 
     */
    public function getInDb()
    {
        return $this->inDb;
    }

    /**
     * Set ts
     *
     * @param \DateTime $ts
     * @return FileStat
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
     * Set project
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project $project
     * @return FileStat
     */
    public function setProject(\CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set collector
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector $collector
     * @return FileStat
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
     * @return FileStat
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
