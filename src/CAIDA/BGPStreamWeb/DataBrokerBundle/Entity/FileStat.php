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
     * Get fileTime
     *
     * @return integer
     */
    public function getFileTime()
    {
        return $this->fileTime;
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
     * Get downloadStart
     *
     * @return integer
     */
    public function getDownloadStart()
    {
        return $this->downloadStart;
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
     * Get inDb
     *
     * @return integer
     */
    public function getInDb()
    {
        return $this->inDb;
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
     * Get project
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
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
