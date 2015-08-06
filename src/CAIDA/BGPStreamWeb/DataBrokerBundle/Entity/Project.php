<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 */
class Project implements JsonSerializable
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
     * Constructor
     */
    public function __construct()
    {
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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set path
     *
     * @param string $path
     * @return Project
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
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
     * Set fileExt
     *
     * @param string $fileExt
     * @return Project
     */
    public function setFileExt($fileExt)
    {
        $this->fileExt = $fileExt;

        return $this;
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
     * Add bgpTypes
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpTypes
     * @return Project
     */
    public function addBgpType(\CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpTypes)
    {
        $this->bgpTypes[] = $bgpTypes;

        return $this;
    }

    /**
     * Remove bgpTypes
     *
     * @param \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpTypes
     */
    public function removeBgpType(\CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType $bgpTypes)
    {
        $this->bgpTypes->removeElement($bgpTypes);
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

    public function jsonSerialize()
    {
        $types = [];
        foreach ($this->getBgpTypes() as $type) {
            /* @var BgpType $type */
            $types[] = $type->getName();
        }
        return [
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'fileExt' => $this->getFileExt(),
            'types' => $types,
        ];
    }
}
