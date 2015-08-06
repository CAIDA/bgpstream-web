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
    private $bgpTypes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $collectors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bgpTypes = new ArrayCollection();
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
     * Get bgpTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBgpTypes()
    {
        return $this->bgpTypes;
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
     * Get collectors by name
     *
     * @param string $name
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollectorsByName($name)
    {
        if ($name) {
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->eq('name', $name));

            return $this->collectors->matching($criteria);
        } else {
            return $this->getCollectors();
        }
    }
}
