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

        $menu->addChild('download',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage',
                            'routeParameters' =>
                                array(
                                    'page' => 'download'
                                )
                        )
        );

        $menu->addChild('documentation',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage_docs',
                            'routeParameters' =>
                                array(
                                    'page' => 'overview'
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

    public
    function docsNavMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('overview',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage_docs',
                            'routeParameters' =>
                                array(
                                    'page' => 'overview'
                                )
                        )
        );

        $menu->addChild('install',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage_docs',
                            'routeParameters' =>
                                array(
                                    'page' => 'install',
                                )
                        )
        );
        $menu['install']->addChild('{BGPStream Core}',
                                   array(
                                       'route'           => 'caida_bgpstream_web_homepage_docs',
                                       'routeParameters' =>
                                           array(
                                               'page'    => 'install',
                                               'subpage' => 'bgpstream'
                                           )
                                   )
        );
        $menu['install']->addChild('{PyBGPStream}',
                                   array(
                                       'route'           => 'caida_bgpstream_web_homepage_docs',
                                       'routeParameters' =>
                                           array(
                                               'page'    => 'install',
                                               'subpage' => 'pybgpstream'
                                           )
                                   )
        );

        $menu->addChild('{Tools & APIs}',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage_docs',
                            'routeParameters' =>
                                array(
                                    'page' => 'tools-apis',
                                )
                        )
        );
        $ta = $menu['{Tools & APIs}'];
        $ta->addChild('{BGPReader}',
                                 array(
                                     'route'           => 'caida_bgpstream_web_homepage_docs',
                                     'routeParameters' =>
                                         array(
                                             'page'    => 'tools',
                                             'subpage' => 'bgpreader',
                                         )
                                 )
        );
        $ta->addChild('{BGPCorsaro}',
                                 array(
                                     'route'           => 'caida_bgpstream_web_homepage_docs',
                                     'routeParameters' =>
                                         array(
                                             'page'    => 'tools',
                                             'subpage' => 'bgpcorsaro',
                                         )
                                 )
        );


        /* C API */
        $ta->addChild('{libBGPStream}',
                                 array(
                                     'route'           => 'caida_bgpstream_web_homepage_docs',
                                     'routeParameters' =>
                                         array(
                                             'page' => 'api',
                                             'subpage' => 'libbgpstream'
                                         )
                                 )
        );
        $tac = $ta['{libBGPStream}'];
        $tac->addChild('{bgpstream.h}',
                                  array(
                                      'route'           => 'caida_bgpstream_web_homepage_docs_api_sphinx',
                                      'routeParameters' =>
                                          array(
                                              'project' => 'libbgpstream',
                                              'file' => 'bgpstream.h.html'
                                          )
                                  )
        );
        $tac->addChild('{bgpstream_record.h}',
                                       array(
                                           'route'           => 'caida_bgpstream_web_homepage_docs_api_sphinx',
                                           'routeParameters' =>
                                               array(
                                                   'project' => 'libbgpstream',
                                                   'file'    => 'bgpstream_record.h.html'
                                               )
                                       )
        );
        $tac->addChild('{bgpstream_elem.h}',
                                       array(
                                           'route'           => 'caida_bgpstream_web_homepage_docs_api_sphinx',
                                           'routeParameters' =>
                                               array(
                                                   'project' => 'libbgpstream',
                                                   'file'    => 'bgpstream_elem.h.html'
                                               )
                                       )
        );

        /* Python API */
        $ta->addChild('{PyBGPStream}',
                                 array(
                                     'route'           => 'caida_bgpstream_web_homepage_docs',
                                     'routeParameters' =>
                                         array(
                                             'page'    => 'api',
                                             'subpage' => 'pybgpstream'
                                         )
                                 )
        );
        $tap = $ta['{PyBGPStream}'];
        $tap->addChild('{_pybgpstream}',
                                       array(
                                           'route'           => 'caida_bgpstream_web_homepage_docs_api_sphinx',
                                           'routeParameters' =>
                                               array(
                                                   'project' => 'pybgpstream',
                                                   'file'    => '_pybgpstream.html'
                                               )
                                       )
        );


        $ta->addChild('{Metadata Broker}',
                                 array(
                                     'route'           => 'caida_bgpstream_web_homepage_docs',
                                     'routeParameters' =>
                                         array(
                                             'page'    => 'api',
                                             'subpage' => 'broker'
                                         )
                                 )
        );

        $menu->addChild('tutorials',
                        array(
                            'route'           => 'caida_bgpstream_web_homepage_docs',
                            'routeParameters' =>
                                array(
                                    'page' => 'tutorials',
                                )
                        )
        );
        $menu['tutorials']->addChild('{libBGPStream}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage_docs',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'libbgpstream'
                                             )
                                     )
        );
        $menu['tutorials']->addChild('{BGPReader}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage_docs',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'bgpreader'
                                             )
                                     )
        );
        $menu['tutorials']->addChild('{BGPCorsaro}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage_docs',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'bgpcorsaro'
                                             )
                                     )
        );
        $menu['tutorials']->addChild('{PyBGPStream}',
                                     array(
                                         'route'           => 'caida_bgpstream_web_homepage_docs',
                                         'routeParameters' =>
                                             array(
                                                 'page'    => 'tutorials',
                                                 'subpage' => 'pybgpstream'
                                             )
                                     )
        );

        return $menu;
    }
}
