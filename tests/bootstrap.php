<?php
/**
 * Test Bootstrap file
 * 
 * @author Aaron Saray
 */

// necessary for PHPUnit in PHPStorm @see https://www.drupal.org/node/2597814
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../vendor/autoload.php');
}

require __DIR__ . '/../vendor/autoload.php';