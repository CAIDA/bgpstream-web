<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function metaAction()
    {
        // parse the request parameters, do a DB lookup and return the results
        return new JsonResponse(['meta' => true]);
    }

    public
    function dataAction()
    {
        // parse the request parameters, do a DB lookup and return the results
        return new JsonResponse(['data' => true]);
    }
}
