<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Collector
 */
class Collector
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project
     */
    private $project;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $data;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bgpTypes;

    /**
     * Constructor
     */
    public
    function __construct()
    {
        $this->data = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bgpTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get bgpTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public
    function getBgpTypes()
    {
        return $this->bgpTypes;
    }
}
