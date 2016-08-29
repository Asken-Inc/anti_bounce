<?php
use Cake\Core\Configure;

Configure::load('AntiBounce.setting');
collection((array)Configure::read('AntiBounce.config'))->each(function ($file) {
    Configure::load($file);
});

/**
 * config/antibounce.php
 *
 * return [
 *   'AntiBounce.topic' => 'arn:aws:sns:us-east-1:****************:***********',
 *   'AntiBounce.mail' => 'your@domain.com',
 * ];
 *
 * config/bootstrap.php
 *
 * Configure::write('AntiBounce.config', ['antibounce']);
 * Plugin::load('AntiBounce', ['routes' => true, 'bootstrap' => true]);
 */