<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


class IntervalSet {

    /** @var Interval[] */
    private $intervals;

    /** @var Interval */
    private $firstInterval;

    public function __construct()
    {
        $this->intervals = [];
        $this->firstInterval = null;
    }

    /**
     * @return Interval[]
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * @param Interval[] $intervals
     */
    public function setIntervals($intervals)
    {
        $this->intervals = $intervals;
    }

    /**
     * @param Interval $interval
     */
    public function addInterval($interval)
    {
        $this->intervals[] = $interval;
        if (!$this->firstInterval || $interval->getStart() < $this->firstInterval->getStart()) {
            $this->firstInterval = $interval;
        }
    }

    public function getFirstInterval()
    {
        return $this->firstInterval;
    }

}