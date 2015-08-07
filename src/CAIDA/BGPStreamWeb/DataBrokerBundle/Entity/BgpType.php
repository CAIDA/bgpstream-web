<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BgpType
 */
class BgpType
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $collectors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collectors = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get collectors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollectors()
    {
        return $this->collectors;
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
}
