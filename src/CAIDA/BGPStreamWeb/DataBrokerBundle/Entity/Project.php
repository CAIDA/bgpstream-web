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
