<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Controller;

use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\CaidaBgpArchive;
use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\Interval;
use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\IntervalSet;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\CollectorType;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\DumpInfo;
use CAIDA\BGPStreamWeb\DataBrokerBundle\HTTP\DataResponse;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    // max window of time to ask from the DB
    const QUERY_WINDOW = 7200;

    private $cacheParams;

    private function serializeDumpInfos($dumpInfos)
    {
        $types = [];
        foreach($dumpInfos as $dumpInfo) {
            /* @var DumpInfo $dumpInfo */
            /* @var CollectorType $ct */
            $ct = $dumpInfo->getCollectorType();
            $types[$ct->getBgpType()->getName()] = [
                'dumpPeriod' => $dumpInfo->getPeriod(),
                'dumpDuration'  => $dumpInfo->getDuration(),
            ];
        }
        return $types;
    }

    private function serializeProjects($projects)
    {
        $data = [];

        foreach ($projects as $project) {
            /* @var Project $project */

            $collectors = [];
            foreach($project->getCollectors() as $collector) {
                /* @var BgpType $type */
                $collectors[] = $collector->getName();
            }

            $data[$project->getName()] = [
                //'name'    => $project->getName(),
                //'path'    => $project->getPath(),
                //'fileExt' => $project->getFileExt(),
                'collectors' => $collectors,
            ];
        }

        return ['projects' => $data];
    }

    private
    function serializeCollectors($collectors)
    {
        $data = [];

        foreach($collectors as $collector) {
            /* @var Collector $collector */

            $data[$collector->getName()] = [
                //'name'       => $collector->getName(),
                //'path'    => $collector->getPath(),
                'project'  => $collector->getProject()->getName(),
                'dataTypes' =>
                    $this->serializeDumpInfos($collector->getDumpInfos()),
            ];
        }

        return ['collectors' => $data];
    }

    /**
     * @param Request $request
     * @param string  $dataResponseType
     *
     * @return DataResponse
     */
    private function setupResponse($request, $dataResponseType)
    {
        // quick, shutdown the session!
        //$request->getSession()->save();
        //session_write_close();

        $response = new DataResponse($dataResponseType);

        $this->cacheParams = [];

        // does the user want us to make the JSON pretty?
        $humanParam          = $request->get('human');
        $human               = isset($humanParam) ? true : false;
        $this->cacheParams[] = $human;
        $response->addOption('human', $human);
        if($human) {
            $response->setEncodingOptions($response->getEncodingOptions() |
                                          JSON_PRETTY_PRINT);
        }

        // is the user specifically requesting big data?
        //$unlimitParam        = $request->get('unlimit');
        //$unlimit             = isset($unlimitParam) ? true : false;
        //$this->cacheParams[] = $unlimit;
        // only allow certain users to request 'unlimit' mode
        //if($unlimit) {
        //    $resp = $this->enableUnlimitMode();
        //    if($resp) {
        //        return $response->setError($resp);
        //    }
        //}

        //$noCacheParam = $request->get('noCache');
        //$resp         = $this->setNoCache(isset($noCacheParam));
        //if($resp) {
        //    return $response->setError($resp);
        //}

        return $response;
    }

    private function getLocalParam($request, $response, $param, $default, $type='string')
    {
        $val            = $request->get($param, $default);

        if ($type == 'array' && !is_array($val)) {
            $val = [$val];
        }

        $this->cacheParams[] = $val;
        $response->addOption($param, $val);

        return $val;
    }

    public function metaProjectsAction($project, Request $request)
    {
        $response = $this->setupResponse($request, DataResponse::TYPE_META);
        if($response->isError()) {
            return $response;
        }

        // handle wildcards
        $project = ($project == '*' || $project == 'all') ? null : $project;

        $response->addOption('project', $project);

        $projects =
            $this->getDoctrine()
                 ->getRepository('CAIDABGPStreamWebDataBrokerBundle:Project')
                 ->findByName($project);
        $response->setData($this->serializeProjects($projects));

        return $response;
    }

    public
    function metaCollectorsAction($collector, Request $request)
    {
        $response = $this->setupResponse($request, DataResponse::TYPE_META);
        if($response->isError()) {
            return $response;
        }

        // handle wildcards
        $collector = ($collector == '*' || $collector == 'all') ? null : $collector;

        $response->addOption('collector', $collector);

        $projects =
            $this->getDoctrine()
                 ->getRepository('CAIDABGPStreamWebDataBrokerBundle:Collector')
                 ->findByName($collector);
        $response->setData($this->serializeCollectors($projects));

        return $response;
    }

    public
    function dataAction(Request $request)
    {
        $response = $this->setupResponse($request, DataResponse::TYPE_DATA);
        if($response->isError()) {
            return $response;
        }

        /* LOCAL PARAMS */

        // projects to retrieve data for
        $projects = $this->getLocalParam($request, $response, 'projects', []);

        // collectors to retrieve data for
        $collectors = $this->getLocalParam($request, $response, 'collectors', []);

        // time intervals to retrieve data for
        $intervalsParam = $this->getLocalParam($request, $response, 'intervals', [], 'array');

        // types to retrieve data for
        $types = $this->getLocalParam($request, $response, 'types', []);

        // TODO: use this fields
        $lastProcessedTime = $this->getLocalParam($request, $response, 'lastProcessedTime', null);
        $lastResponseId = $this->getLocalParam($request, $response, 'lastResponseId', null);

        // some sanity checking on the parameters
        if (count($intervalsParam) == 0) {
            return $response->setError("At least one interval must be set");
        }

        // parse the intervals, compressing overlaps
        $intervals = new IntervalSet(true);
        foreach ($intervalsParam as $intStr) {
            try {
                $interval = new Interval($intStr);
            } catch (\InvalidArgumentException $ex) {
                return $response->setError($ex->getMessage());
            }
            $intervals->addInterval($interval);
        }


        // TODO: use the last processed, and last query to set start of constraint
        $firstInterval = $intervals->getFirstInterval();

        $constraintInterval = new Interval(
            $firstInterval->getStart(),
            $firstInterval->getStart() + static::QUERY_WINDOW
        );

        /* guaranteed to be sorted by start time */
        $bgpdata =
            $this->getDoctrine()
                 ->getRepository('CAIDABGPStreamWebDataBrokerBundle:BgpData')
                 ->findByIntervalProjectsCollectorsTypes($intervals,
                                                         $constraintInterval,
                                                         $projects,
                                                         $collectors,
                                                         $types);

        $dumpfiles = CaidaBgpArchive::generateDumpFiles($bgpdata);

        $response->addOption('debug', [
                                        'numFiles' => count($dumpfiles->getDumpFiles()),
                                        'intervals' => $intervals,
                                    ]
        );

        $response->setData(['dumpFiles' => $dumpfiles]);
        return $response;
    }
}
