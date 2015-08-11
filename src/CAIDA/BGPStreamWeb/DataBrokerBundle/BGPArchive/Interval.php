<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use JsonSerializable;

class Interval implements JsonSerializable {

    private $start;
    private $end;

    /**
     * @param mixed $start
     * @param integer|null $end
     * @return Interval
     */
    public function __construct($start, $end=null)
    {
        if (is_string($start) && $end == null) {
            $arr = explode(',', $start);
            if (count($arr) != 2) {
                throw new \InvalidArgumentException('Invalid Interval String: ' . $start);
            }
            return $this->__construct((int)$arr[0], (int)$arr[1]);
        }

        if (!is_numeric($start) || !is_numeric($end)) {
            throw new \InvalidArgumentException('Invalid Interval: ' . $start . ',' . $end);
        }

        if ($start > $end) {
            throw new \InvalidArgumentException('Invalid Interval: ' . $start . ',' . $end);
        }

        $this->setStart($start);
        $this->setEnd($end);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @param Interval $interval
     * @return boolean
     */
    public function extendOverlapping($interval) {
        if ($interval->getStart() == $this->getStart() &&
            $interval->getEnd() == $this->getEnd()) {
            // they are identical
            return true;
        }
        if ($interval->getStart() >= $this->getEnd()) {
            return false;
        }

        if ($interval->getEnd() > $this->getEnd()) {
            $this->setEnd($interval->getEnd());
        }

        return true;
    }

    public function __toString()
    {
        return $this->getStart() . ',' . $this->getEnd();
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }

}
