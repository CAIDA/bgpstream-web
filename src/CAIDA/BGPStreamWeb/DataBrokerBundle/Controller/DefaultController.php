<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Controller;

use CAIDA\BGPStreamWeb\DataBrokerBundle\BGPArchive\CaidaBgpArchive;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpData;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\OnWebFrequency;
use CAIDA\BGPStreamWeb\DataBrokerBundle\HTTP\DataResponse;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    // max window of time to ask from the DB
    const QUERY_WINDOW = 7200;

    private $cacheParams;

    private function serializeBgpTypes($onWebFreqs)
    {
        $types = [];
        foreach($onWebFreqs as $freq) {
            /* @var OnWebFrequency $freq */
            $types[$freq->getBgpType()->getName()] = [
                'dumpFrequency' => $freq->getOnWebFreq(),
                'dumpDuration'  => $freq->getOffset(),
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
                'dataTypes' => $this->serializeBgpTypes($project->getOnWebFrequencies()),
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
                    $this->serializeBgpTypes($collector->getProject()
                                                 ->getOnWebFrequencies()),
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
        $projects = $this->getLocalParam($request, $response, 'projects', []);
        $collectors = $this->getLocalParam($request, $response, 'collectors', []);
        $intervals = $this->getLocalParam($request, $response, 'intervals', [], 'array');
        $types = $this->getLocalParam($request, $response, 'types', []);

        // TODO: param last processing bgp time
        // TODO: param last response time
        // TODO: send this response time

        // some sanity checking on the parameters
        if (count($intervals) == 0) {
            return $response->setError("At least one interval must be set");
        }

        // parse the intervals and find the first interval that we should deal with
        // TODO: convert this to an Interval class
        $intervalArr = [];
        $firstInterval = null;
        foreach ($intervals as $interval) {
            $arr = explode(',', $interval);
            if (count($arr) != 2 || !is_numeric($arr[0]) || !is_numeric($arr[1])) {
                return $response->setError('Invalid interval: ' . $interval);
            }
            $arr[0] = (int)$arr[0];
            $arr[1] = (int)$arr[1];
            if (!$firstInterval || $arr[0] < $firstInterval[0]) {
                $firstInterval = $arr;
            }
            $intervalArr[] = $arr;
        }
        // truncate the interval to our max window len
        $firstInterval[1] = min($firstInterval[1],
                                $firstInterval[0] + static::QUERY_WINDOW);

        $bgpdata =
            $this->getDoctrine()
                 ->getRepository('CAIDABGPStreamWebDataBrokerBundle:BgpData')
                 ->findByIntervalProjectsCollectorsTypes($firstInterval[0],
                                                         $firstInterval[1],
                                                         $projects,
                                                         $collectors,
                                                         $types);

        $dumpfiles = CaidaBgpArchive::generateDumpFiles($bgpdata);

        $response->setData(['dumpFiles' => $dumpfiles]);
        return $response;
    }
}
