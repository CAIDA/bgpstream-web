<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;

interface BgpArchiveInterface {

    /**
     * @param BgpData[] $bgpData
     *
     * @return DumpFileSet
     */
    public static function generateDumpFiles($bgpData);

}
