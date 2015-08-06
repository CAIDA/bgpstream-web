<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Controller;

use CAIDA\BGPStreamWeb\DataBrokerBundle\HTTP\DataResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    private $cacheParams;

    /**
     * @param Request $request
     * @param string  $dataResponseType
     *
     * @return DataResponse
     */
    private function setupResponse($request)
    {
        // quick, shutdown the session!
        //$request->getSession()->save();
        //session_write_close();

        $response = new DataResponse();

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

    public function metaAction($type, Request $request)
    {
        $response = $this->setupResponse($request);

        if ($type == "projects") {
            $products =
                $this->getDoctrine()
                    ->getRepository('CAIDABGPStreamWebDataBrokerBundle:Project')
                    ->findAll();
            $response->setData($products);
        } else {
            throw new NotFoundHttpException('Invalid metadata type');
        }

        return $response;
    }

    public
    function dataAction(Request $request)
    {
        $response = $this->setupResponse($request);
        $response->setData(['data' => true]);
        return $response;
    }
}
