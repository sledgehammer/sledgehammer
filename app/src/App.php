<?php

namespace App;

use Sledgehammer\Core\Json;
use Sledgehammer\Mvc\Component\Alert;
use Sledgehammer\Mvc\Component\Nav;
use Sledgehammer\Mvc\Component\Template;
use Sledgehammer\Mvc\Website;
use const Sledgehammer\PATH;
use const Sledgehammer\WEBROOT;

/**
 * Example App
 */
class App extends Website
{

    /**
     * Public methods are accessable as file and must return a View object.
     * "/index.html"
     * @return View
     */
    function index()
    {
        return new Nav([
            'Welcome',
            WEBROOT.'example/item1.html' => 'Item 1',
            WEBROOT.'service.json' => 'Item 2',
            ], [
            'class' => 'nav nav-list'
        ]);
    }

    /**
     * Public methods with the "_folder" suffix are accesable as folder.
     * "/example/*"
     * @param string $file
     * @return View
     */
    function example_folder($file)
    {
        return new Alert('This is page: '.$file);
    }

    function service()
    {
        return new Json(['success' => true]);
    }

    protected function wrapContent($view)
    {
        $headers = [
            'title' => 'Sledgehammer App',
            'css' => WEBROOT.'mvc/css/bootstrap.css',
        ];
        return new Template(PATH.'app/templates/layout.php', ['content' => $view], $headers);
    }

}
