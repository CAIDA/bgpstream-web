<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


class DumpFileSet implements \JsonSerializable {

    /**
     * @var DumpFile[]
     */
    private $dumpFiles;

    public function __construct()
    {
        $this->dumpFiles = [];
    }

    /**
     * @return DumpFile[]
     */
    public function getDumpFiles()
    {
        return $this->dumpFiles;
    }

    /**
     * @param DumpFile[] $dumpFiles
     */
    public function setDumpFiles($dumpFiles)
    {
        $this->dumpFiles = $dumpFiles;
    }

    public function addDumpFile($dumpFile)
    {
        $this->dumpFiles[] = $dumpFile;
    }

    public function jsonSerialize()
    {
        return $this->getDumpFiles();
    }

}