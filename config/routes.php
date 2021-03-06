<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'Uluru/AntiBounce',
    ['path' => '/anti-bounce'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
