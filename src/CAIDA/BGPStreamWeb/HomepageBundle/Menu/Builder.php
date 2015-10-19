<?php

namespace CAIDA\BGPStreamWeb\HomepageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{
    private $factory;
    protected $authorizationCheckerInterface;

    /**
     * @param FactoryInterface                                          $factory
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationCheckerInterface
     */
    public function __construct(FactoryInterface $factory,
                                AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->factory = $factory;
        $this->authorizationCheckerInterface = $authorizationCheckerInterface;
    }

    public function topNavMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('home',
                        array(
                            'route' => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'home'
                                )
                        )
        );

        $menu->addChild('overview',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page'    => 'docs',
                                    'subpage' => 'overview'
                                )
                        )
        );

        $menu->addChild('download',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'download'
                                )
                        )
        );

        $menu->addChild('install');
        $menu['install']->addChild('{BGPStream}',
                                array(
                                    'route'           => 'caida_bgpstream_web_homepage',
                                    'routeParameters' =>
                                        array(
                                            'page'    => 'install',
                                            'subpage' => 'bgpstream'
                                        )
                                )
        );
        $menu['install']->addChild('{PyBGPStream}',
                                   array(
                                       'route'           => 'caida_bgpstream_web_homepage',
                                       'routeParameters' =>
                                           array(
                                               'page'    => 'install',
                                               'subpage' => 'pybgpstream'
                                           )
                                   )
        );

        $menu->addChild('{API}');

        $menu['{API}']->addChild('{C API}',
                                array(
                                    'route'           => 'caida_bgpstream_web_homepage_docs_api',
                                    'routeParameters' =>
                                        array(
                                            'doxypage'    => 'bgpstream_8h.html',
                                        )
                                )
        );
        $menu['{API}']->addChild('{Python API}',
                                array(
                                    'route'           => 'caida_bgpstream_web_homepage',
                                    'routeParameters' =>
                                        array(
                                            'page'    => 'docs',
                                            'subpage' => 'pybgpstream-api'
                                        )
                                )
        );
        $menu['{API}']->addChild('{Broker API}',
                                array(
                                    'route'           => 'caida_bgpstream_web_homepage',
                                    'routeParameters' =>
                                        array(
                                            'page'    => 'docs',
                                            'subpage' => 'broker-api'
                                        )
                                )
        );

        $menu->addChild('tutorials');
        $menu['tutorials']->addChild('{libBGPStream}',
                                array(
                                    'route'           => 'caida_bgpstream_web_homepage',
                                    'routeParameters' =>
                                        array(
                                            'page'    => 'tutorials',
                                            'subpage' => 'libbgpstream'
                                        )
                                )
        );
        $menu['tutorials']->addChild('{BGPReader}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'bgpreader'
                                             )
                                     )
        );
        $menu['tutorials']->addChild('{BGPCorsaro}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'bgpcorsaro'
                                             )
                                     )
        );
        $menu['tutorials']->addChild('{PyBGPStream}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'pybgpstream'
                                             )
                                     )
        );

        $menu->addChild('publications',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'pubs'
                                )
                        )
        );

        $menu->addChild('data providers',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'data'
                                )
                        )
        );

        /*
        $menu->addChild('projects',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'projects'
                                )
                        )
        );
        */

        // short circuit here if not logged in at all.
        // anything below here WILL NOT be visible to anon visitors
        /*
        if(!$this->authorizationCheckerInterface->isGranted('ROLE_USER')) {
            return $menu;
        }

        $menu->addChild('explorer',
                        array(
                            'route'           => 'caida_charthouse_frontend_default',
                            'routeParameters' =>
                                array(
                                    'page' => 'explorer'
                                )
                        )
        );

        $menu->addChild('admin');
        $menu['admin']->addChild('users',
                                 array('route' => 'caida_charthouse_security_admin',
                                       'routeParameters' =>
                                           array(
                                               'page' => 'users'
                                           )
                                 )
        );
        */

        return $menu;
    }
}
