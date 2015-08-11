<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


class CaidaBgpArchive implements BgpArchiveInterface {

    const ARCHIVE_PATH = "http://bgp-archive.caida.org";

    public static function generateDumpFiles($bgpdata)
    {
        $dfs = new DumpFileSet();

        /* @var Interval $interval */
        $interval = null;
        foreach ($bgpdata as $row) {

            // does this file overlap with our interval?
            $ti = new Interval(
                // ribs span [ts-120, ts+120]
                $row->getBgpType()->getName() == "ribs" ?
                    $row->getFileTime()-$row->getDumpInfo()->getDuration() :
                    $row->getFileTime(),
                $row->getFileTime() +
                $row->getDumpInfo()->getDuration()
            );
            if (!$interval) {
                $interval = $ti;
            } else if (!$interval->extendOverlapping($ti)) {
                // skip this file
                continue;
            }

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
