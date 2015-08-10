<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


class Interval {

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
            return $this->__construct($arr[0], $arr[1]);
        }

        if (!is_numeric($start) || !is_numeric($end)) {
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

}