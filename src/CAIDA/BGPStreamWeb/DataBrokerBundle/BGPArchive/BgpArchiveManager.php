<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 8/21/15
 * Time: 2:09 PM
 */

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


use CAIDA\BGPStreamWeb\DataBrokerBundle\DumpFile\DumpFileSet;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BgpArchiveManager {

    /**
     * @var BgpArchiveInterface[]
     */
    private $archives = [];


    public function __construct()
    {
        $this->archives[] = new LocalBgpArchive();
        $this->archives[] = new RouteviewsBgpArchive();
        $this->archives[] = new RisBgpArchive();
    }

    /**
     * @param Request $request
     * @param BgpData[] $bgpdata
     * @return DumpFileSet
     * @throws AccessDeniedHttpException
     */
    public function generateDumpFiles($request, $bgpdata)
    {
        $dfs = new DumpFileSet();
        foreach ($bgpdata as $row) {
            $selected = null;
            foreach ($this->archives as $archive) {
                if ($archive->acceptBgpData($request, $row)) {
                    $selected = $archive;
                    break;
                }
            }
            // if no archive hosts this file (for this user), then they don't get it!
            if ($selected) {
                $dfs->addDumpFile($selected->generateDumpFile($row));
            } else {
                // TODO: remove this (debugging only)
                throw new AccessDeniedHttpException();
            }
        }
        return $dfs;
    }

}
