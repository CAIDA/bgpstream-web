<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive;


use CAIDA\BGPStreamWeb\DataBrokerBundle\DumpFile\DumpFile;

class CaidaBgpArchive implements BgpArchiveInterface {

    const ARCHIVE_PATH = "http://bgp-archive.caida.org";

    private $allowedRanges = [
        '192.172.226.0/24', // CAIDA
        '198.202.104.0/24', // SDSC Gordon
    ];

    /**
     * Check if a given ip is in a network
     *
     * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     *
     * @return boolean true if the ip is in this range / false if not.
     */
    private function ip_in_range($ip, $range)
    {
        if(strpos($range, '/') == false) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list($range, $netmask) = explode('/', $range, 2);
        $range_decimal    = ip2long($range);
        $ip_decimal       = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal  = ~$wildcard_decimal;

        return (($ip_decimal & $netmask_decimal) ==
                ($range_decimal & $netmask_decimal));
    }

    public function acceptBgpData($request, $row)
    {
        // allow caida users to pull data directly from RV/RIS
        $preferCaida = $request->get('preferCaida', 'true');
        $preferCaida = $preferCaida == 'true' ? true : false;
        if(!$preferCaida) {
            return false;
        }
        foreach ($this->allowedRanges as $pfx) {
            if($this->ip_in_range($request->getClientIp(), $pfx)) {
                return true;
            }
        }

        return false;
    }

    public function generateDumpFile($row)
    {
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
