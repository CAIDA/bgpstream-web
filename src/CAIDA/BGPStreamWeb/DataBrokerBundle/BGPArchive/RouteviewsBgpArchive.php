<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


use CAIDA\BGPStreamWeb\DataBrokerBundle\DumpFile\DumpFile;

class RouteviewsBgpArchive implements BgpArchiveInterface {

    const ARCHIVE_PATH = "http://archive.routeviews.org";

    public function acceptBgpData($request, $row)
    {
        return ($row->getCollector()->getProject()->getName() == 'routeviews');
    }

    public function generateDumpFile($row)
    {
        $url =
            static::ARCHIVE_PATH . '/' .
            ($row->getCollector()->getName() == 'route-views2' ? '' : $row->getCollector()->getPath() . '/') .
            'bgpdata/' .
            date('Y.m', $row->getFileTime()) . '/' .
            strtoupper($row->getBgpType()->getPath()) . '/' .
            ($row->getBgpType()->getName() == 'ribs' ? 'rib' : $row->getBgpType()->getPath()) . '.' .
            date('Ymd.Hi', $row->getFileTime()) . '.' .
            $row->getCollector()->getProject()->getFileExt();

        return new DumpFile(
            DumpFile::DUMP_TYPE_SIMPLE,
            $url,
            $row->getCollector()->getProject()->getName(),
            $row->getCollector()->getName(),
            $row->getBgpType()->getName(),
            $row->getFileTime(),
            $row->getDumpInfo()->getDuration()
        );
    }

}
