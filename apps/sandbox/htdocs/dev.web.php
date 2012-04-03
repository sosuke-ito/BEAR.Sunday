<?php

use BEAR\Framework\StandardRouter as Router;
use BEAR\Framework\Dispatcher;
use BEAR\Framework\Globals;
use BEAR\Framework\Web\HttpFoundation as Res;
use BEAR\Framework\Web\HttpFoundation as Output;

/**
 * CLI / Built-in web server dev script
 *
 * examaple:
 *
 * CLI:
 * $ php dev.web.php get /hello
 *
 * Built-in web server:
 *
 * $ php -S localhost:8080 dev.web/php
 *
 * type URL:
 *   http://localhost:8080/hello
 *   http://localhost:8080/helloresource
 *
 * @package BEAR.Framework
 *
 * @global string               $method   Resource method
 * @global BEAR\Resource\Client $resource Resource client
 * @global array                $query    Resource request query
 * @global BEAR\Resource\Object $page     Resource object (target)
 * @global BEAR\Resource\Object $response Resource object (response)
 */
if (php_sapi_name() == 'cli-server') {
    // route static assets and return false
    if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css)$/', $_SERVER["REQUEST_URI"])) {
        return false;
    }
}

// Application
$app = require dirname(__DIR__) . '/scripts/instance.php';

// Route
$globals = (PHP_SAPI === 'cli') ? new Globals($argv) : $GLOBALS;
// $router = require dirname(__DIR__) . '/scripts/router/standard_router.php';
$router = new Router; // no router

// Dispatch
list($method, $pagePath, $query) = $router->match($globals);

// Request
$page = $app->resource->$method->uri('page://self/' . $pagePath)->withQuery($query)->eager->request();

(new Output)->debug()->setResource($page)->prepare()->output();