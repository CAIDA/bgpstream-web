<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use JsonSerializable;

/**
 * Class DumpFile
 */
class DumpFile implements JsonSerializable {

    const DUMP_TYPE_SIMPLE = "simple";

    const DUMP_TYPE_SYMURL = "sym";

    const DUMP_TYPE_BGPMON = "bgpmon";

    /**
     * @var string
     */
    private $urlType;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $project;

    /**
     * @var string
     */
    private $collector;

    /**
     * @var string
     */
    private $bgpType;

    /**
     * @var integer
     */
    private $initialTime;

    /**
     * @var integer
     */
    private $duration;

    /**
     * Constructor
     */
    public function __construct($urlType, $url, $project, $collector,
                                 $bgpType, $initialTime, $duration)
    {
        $this->urlType = $urlType;
        $this->url = $url;
        $this->project = $project;
        $this->collector = $collector;
        $this->bgpType = $bgpType;
        $this->initialTime = $initialTime;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public
    function getUrlType()
    {
        return $this->urlType;
    }

    /**
     * @return string
     */
    public
    function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public
    function getProject()
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public
    function getCollector()
    {
        return $this->collector;
    }

    /**
     * @return string
     */
    public
    function getBgpType()
    {
        return $this->bgpType;
    }

    /**
     * @return int
     */
    public
    function getInitialTime()
    {
        return $this->initialTime;
    }

    /**
     * @return int
     */
    public
    function getDuration()
    {
        return $this->duration;
    }

    public function jsonSerialize()
    {
        return [
            'urlType' => $this->getUrlType(),
            'url'  => $this->getUrl(),
            'project' => $this->getProject(),
            'collector' => $this->getCollector(),
            'type' => $this->getBgpType(),
            'initialTime' => $this->getInitialTime(),
            'duration' => $this->getDuration(),
        ];
    }

}
