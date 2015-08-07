<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;

interface BgpArchiveInterface {

    /**
     * @param BgpData[] $bgpData
     *
     * @return DumpFile[]
     */
    public static function generateDumpFiles($bgpData);

}
