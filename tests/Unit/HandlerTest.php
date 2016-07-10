<?php
/**
 * Handler Unit test
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger\Tests\Unit;

use AaronSaray\PHProblemLogger\Handler;

/**
 * Class HandlerTest
 * @package AaronSaray\PHProblemLogger\Tests\Unit
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array using this a lot for the filter tests
     */
    protected $defaultValuesArray = [
        'session'       =>  null,
        'get'           =>  null,
        'post'          =>  null,
        'cookie'        =>  null,
        'environment'   =>  null,
        'server'        =>  null
    ];

    /**
     * @return \Closure the plain callable that does nothing but return the payload
     */
    protected function getDumbCallable()
    {
        return function(array $payload) {
            return $payload;
        };
    }
    
    public function testSessionFilter()
    {
        $handler = new Handler();
        $_SESSION = ['test-item-session'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->session($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['session' => ['test-item-session']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testGetFilter()
    {
        $handler = new Handler();
        $_GET = ['test-item-get'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->get($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['get' => ['test-item-get']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testPostFilter()
    {
        $handler = new Handler();
        $_POST = ['test-item-post'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->post($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['post' => ['test-item-post']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testCookieFilter()
    {
        $handler = new Handler();
        $_COOKIE = ['test-item-cookie'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->cookie($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['cookie' => ['test-item-cookie']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testEnvironmentFilter()
    {
        $handler = new Handler();
        $_ENV = ['test-item-environment'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->environment($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['environment' => ['test-item-environment']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testServerFilter()
    {
        $handler = new Handler();
        $_SERVER = ['test-item-server'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->server($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['server' => ['test-item-server']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }
}