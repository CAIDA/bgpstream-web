<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OnWebFrequency
 */
class OnWebFrequency
{
    /**
     * @var integer
     */
    private $onWebFreq;

    /**
     * @var integer
     */
    private $offset;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project
     */
    private $project;

    /**
     * @var \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    private $bgpType;

    /**
     * Get onWebFreq
     *
     * @return integer
     */
    public function getOnWebFreq()
    {
        return $this->onWebFreq;
    }

    /**
     * Get offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
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
     * Get bgpType
     *
     * @return \CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType
     */
    public function getBgpType()
    {
        return $this->bgpType;
    }
}
