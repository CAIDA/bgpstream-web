<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


use CAIDA\BGPStreamWeb\DataBrokerBundle\DumpFile\DumpFile;

class RisBgpArchive implements BgpArchiveInterface {

    const ARCHIVE_PATH = "http://data.ris.ripe.net";

    public function acceptBgpData($request, $row)
    {
        return ($row->getCollector()->getProject()->getName() == 'ris');
    }

    public function generateDumpFile($row)
    {
        $url =
            static::ARCHIVE_PATH . '/' .
            $row->getCollector()->getPath() . '/' .
            date('Y.m', $row->getFileTime()) . '/' .
            ($row->getBgpType()->getName() == 'ribs' ? 'bview' : $row->getBgpType()->getPath()) . '.' .
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
