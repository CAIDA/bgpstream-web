<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Controller;

use CAIDA\BGPStreamWeb\DataBrokerBundle\HTTP\DataResponse;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Project;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\BgpType;
use CAIDA\BGPStreamWeb\DataBrokerBundle\Entity\Collector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    private $cacheParams;

    public function serializeProjects($projects, $collectorFilter)
    {
        $data = [];

        foreach ($projects as $project) {
            /* @var Project $project */

            $types = [];
            foreach($project->getBgpTypes() as $type) {
                /* @var BgpType $type */
                $types[] = $type->getName();
            }

            $collectors = [];
            foreach($project->getCollectorsByName($collectorFilter) as $collector) {
                /* @var BgpType $type */
                $collectors[] = $collector->getName();
            }

            $data[] = [
                'name'    => $project->getName(),
                //'path'    => $project->getPath(),
                //'fileExt' => $project->getFileExt(),
                'dataTypes'   => $types,
                'collectors' => $collectors,
            ];
        }

        return ['projects' => $data];
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

    public function metaAction($project, $collector, Request $request)
    {
        $response = $this->setupResponse($request, DataResponse::TYPE_META);

        // handle wildcards
        $project = ($project == '*' || $project == 'all') ? null : $project;
        $collector = ($collector == '*' || $collector == 'all') ? null : $collector;

        $response->addOption('project', $project);
        $response->addOption('collector', $project);

        $projects =
            $this->getDoctrine()
                 ->getRepository('CAIDABGPStreamWebDataBrokerBundle:Project')
                 ->findByName($project);
        $response->setData($this->serializeProjects($projects, $collector));

        return $response;
    }

    public
    function dataAction(Request $request)
    {
        $response = $this->setupResponse($request, DataResponse::TYPE_DATA);
        $response->setData(['data' => true]);
        return $response;
    }
}
