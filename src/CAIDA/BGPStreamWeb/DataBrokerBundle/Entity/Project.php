<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 */
class Project
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
     * @var string
     */
    private $fileExt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $collectors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collectors = new ArrayCollection();
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
     * Get fileExt
     *
     * @return string
     */
    public function getFileExt()
    {
        return $this->fileExt;
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

    public
    function __toString()
    {
        return $this->getName();
    }
}
