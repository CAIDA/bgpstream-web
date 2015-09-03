<?php

namespace CAIDA\BGPStreamWeb\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($_route, $page, $subpage)
    {
        /* if they have asked for 'index', send them to 'home' */
        if($page == 'index') {
            $page = 'home';
        }

        if(isset($subpage)) {
            $twig =
                "CAIDABGPStreamWebHomepageBundle:Default:$page.$subpage.html.twig";
        } else {
            $twig = "CAIDABGPStreamWebHomepageBundle:Default:$page.html.twig";
        }

        try {
            return $this->render($twig,
                                 array(
                                     'route'                => $_route,
                                 )
            );
        } catch(\InvalidArgumentException $ex) {
            throw $this->createNotFoundException('Page \'' . $page .
                                                 '\' does not exist');
        }
    }
}
