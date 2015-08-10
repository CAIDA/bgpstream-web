<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


class CaidaBgpArchive implements BgpArchiveInterface {

    const ARCHIVE_PATH = "http://bgp-archive.caida.org";

    public static function generateDumpFiles($bgpdata)
    {
        $dfs = new DumpFileSet();
        foreach ($bgpdata as $row) {

            $url =
                static::ARCHIVE_PATH . '/' .
                $row->getCollector()->getProject()->getPath() . '/' .
                $row->getCollector()->getPath() . '/' .
                $row->getBgpType()->getPath() . '/' .
                date('Y/m/d', $row->getFileTime()) . '/' .
                $row->getCollector()->getProject()->getPath() . '.' .
                $row->getCollector()->getPath() . '.' .
                $row->getBgpType()->getPath() . '.' .
                $row->getFileTime() . '.' .
                $row->getCollector()->getProject()->getFileExt();

            $dfs->addDumpFile(
                new DumpFile(
                    DumpFile::DUMP_TYPE_SIMPLE,
                    $url,
                    $row->getCollector()->getProject()->getName(),
                    $row->getCollector()->getName(),
                    $row->getBgpType()->getName(),
                    $row->getFileTime(),
                    $row->getDumpInfo()->getDuration()
                )
            );
        }

        return $dfs;
    }

}
