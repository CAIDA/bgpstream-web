<?php
/*
 * Copyright (C) 2014 The Regents of the University of California.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

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

    public
    function sphinxAction($_route, $project, $file)
    {
        $twig =
            "CAIDABGPStreamWebHomepageBundle:Default:docs/api/sphinx-docs.html.twig";

        if(substr_compare($file, '.html', -5) == 0) {
            $file = substr($file, 0, -5);
        }

        $sphinxFile =
            "@CAIDABGPStreamWebHomepageBundle/Resources/content/docs/api/$project/$file.html";

        try {
            return $this->render($twig,
                                 array(
                                     'route'           => $_route,
                                     'file_path' => $sphinxFile,
                                     'filename' => $file,
                                 )
            );
        } catch(\InvalidArgumentException $ex) {
            throw $this->createNotFoundException('Page does not exist');
        }
    }

    public
    function docsAction($_route, $page, $subpage)
    {
        /* if they have asked for 'home' or 'index', send them to 'overview' */
        if($page == 'home' || $page == 'index') {
            $page = 'overview';
        }

        // first try and render markdown
        if (isset($subpage)) {
            $mdFile =
                "@CAIDABGPStreamWebHomepageBundle/Resources/content/docs/$page/$subpage.md";
        } else {
            $mdFile =
                "@CAIDABGPStreamWebHomepageBundle/Resources/content/docs/$page.md";
        }
        if ($this->get('templating')->exists($mdFile)) {
            $mdTwig =
                "CAIDABGPStreamWebHomepageBundle:Default:docs/markdown-content.html.twig";
            try {
                return $this->render($mdTwig,
                                     array(
                                         'route'           => $_route,
                                         'md_content_file' => $mdFile,
                                     )
                );
            } catch(\InvalidArgumentException $ex) {
                throw $this->createNotFoundException('Page \'' . $page .
                                                     '\' does not exist');
            }
        } else {
            if(isset($subpage)) {
                $twig =
                    "CAIDABGPStreamWebHomepageBundle:Default:docs/$page/$subpage.html.twig";
            } else {
                $twig =
                    "CAIDABGPStreamWebHomepageBundle:Default:docs/$page.html.twig";
            }

            try {
                return $this->render($twig,
                                     array(
                                         'route' => $_route,
                                     )
                );
            } catch(\InvalidArgumentException $ex) {
                throw $this->createNotFoundException('Page \'' . $page .
                                                     '\' does not exist ');
            }
        }

    }
}
