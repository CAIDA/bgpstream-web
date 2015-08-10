<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use JsonSerializable;

class IntervalSet implements JsonSerializable {

    /** @var boolean */
    private $combineOverlapping;

    /** @var Interval[] */
    private $intervals;

    /** @var Interval */
    private $firstInterval;

    public function __construct($combineOverlapping=false)
    {
        $this->intervals = [];
        $this->firstInterval = null;
        $this->combineOverlapping = $combineOverlapping;
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
        $toAdd = true;
        if ($this->combineOverlapping) {
            // check if any of the existing intervals overlap
            foreach ($this->getIntervals() as $exInt) {
                if ($exInt->getStart() > $interval->getEnd()) {
                    break;
                }
                if ($exInt->extendOverlapping($interval)) {
                    // we merged this interval into an existing one
                    $toAdd = false;
                    break;
                }
            }
        }
        if($toAdd) {
            $this->intervals[] = $interval;
        }

        // now re-sort
        $this->update();
    }

    public function getFirstInterval()
    {
        return $this->firstInterval;
    }

    public function jsonSerialize()
    {
        return $this->getIntervals();
    }

    /**
     * @param Interval $a
     * @param Interval $b
     *
     * @return integer
     */
    private static function cmpInterval($a, $b)
    {
        return $a->getStart() - $b->getStart();
    }

    private function update()
    {
        // re-sort the array, and look for the first interval
        usort($this->intervals, ['CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\IntervalSet', 'cmpInterval']);

        $this->firstInterval = null;
        foreach ($this->intervals as $interval) {
            if(!$this->firstInterval ||
               $interval->getStart() < $this->firstInterval->getStart()
            ) {
                $this->firstInterval = $interval;
            }
        }
    }

}
