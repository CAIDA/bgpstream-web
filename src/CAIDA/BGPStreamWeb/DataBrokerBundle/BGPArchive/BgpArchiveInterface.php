<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;

use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use Symfony\Component\HttpFoundation\Request;

interface BgpArchiveInterface {

    /**
     * @param Request $request
     * @param BgpData $bgpdata
     *
     * @return boolean
     */
    public function acceptBgpData($request, $bgpdata);

    /**
     * @param BgpData $bgpData
     *
     * @return DumpFile
     */
    public function generateDumpFile($bgpData);

}
