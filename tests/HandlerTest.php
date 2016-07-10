<?php
/**
 * Handler Unit test
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger\Tests;

use AaronSaray\PHProblemLogger\Handler;
use AaronSaray\PHProblemLogger\HandlerFilter;
use AaronSaray\PHProblemLogger\Tests\LogSaver;
use Psr\Log\NullLogger;

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
        'server'        =>  null,
        'application'   =>  null
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
        $handler = new Handler(new NullLogger());
        $_SESSION = ['test-item-session'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->session($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['session' => ['test-item-session']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testGetFilter()
    {
        $handler = new Handler(new NullLogger());
        $_GET = ['test-item-get'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->get($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['get' => ['test-item-get']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testPostFilter()
    {
        $handler = new Handler(new NullLogger());
        $_POST = ['test-item-post'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->post($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['post' => ['test-item-post']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testCookieFilter()
    {
        $handler = new Handler(new NullLogger());
        $_COOKIE = ['test-item-cookie'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->cookie($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['cookie' => ['test-item-cookie']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testEnvironmentFilter()
    {
        $handler = new Handler(new NullLogger());
        $_ENV = ['test-item-environment'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->environment($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['environment' => ['test-item-environment']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testServerFilter()
    {
        $handler = new Handler(new NullLogger());
        $_SERVER = ['test-item-server'];
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->server($this->getDumbCallable()));
        $assumedValue = array_merge($this->defaultValuesArray, ['server' => ['test-item-server']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    public function testApplicationFilter()
    {
        $handler = new Handler(new NullLogger());
        $callable = function(array $payload) {
            $payload['custom-value'] = 2;
            return $payload;
        };
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $handler->application($callable));
        $assumedValue = array_merge($this->defaultValuesArray, ['application' => ['custom-value' => 2]]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }
    
    public function testBuiltInFilterUsage()
    {
        $handler = new Handler(new NullLogger());
        $_SERVER = ['something'];
        $handler->server(HandlerFilter::all());
        $assumedValue = array_merge($this->defaultValuesArray, ['server' => ['something']]);
        $this->assertAttributeEquals($assumedValue, 'values', $handler);
    }

    /**
     * @runInSeparateProcess
     */
    public function testPreviousExceptionHandlerNull()
    {
        $handler = new Handler(new NullLogger());
        $this->assertAttributeEquals(null, 'previousExceptionHandler', $handler);
    }
    
    public function testPreviousExceptionHandlerWasKept()
    {
        set_exception_handler('trim');
        $handler = new Handler(new NullLogger());
        $this->assertAttributeEquals('trim', 'previousExceptionHandler', $handler);
    }
    
    public function testExceptionHandlerWasSet()
    {
        new Handler(new NullLogger());
        $currentExceptionHandler = set_exception_handler(function(){});
        $this->assertTrue(is_array($currentExceptionHandler));
        $this->assertInstanceOf('AaronSaray\PHProblemLogger\Handler', $currentExceptionHandler[0]);
        $this->assertEquals('handleException', $currentExceptionHandler[1]);
    }
    
    public function testExceptionHandlerFiltersOutNull()
    {
        $_SERVER['some-item'] = true; // make sure this has at least one thing in it
        $logSaver = new LogSaver();
        
        $handler = new Handler($logSaver);
        $handler->server(HandlerFilter::all());
        try {
            $handler->handleException(new \Exception());
        }
        catch (\Exception $e) {
            // do nothing 
        }

        $this->assertCount(1, $logSaver->context);
        $this->assertArrayHasKey('server', $logSaver->context);
        $this->assertNotEmpty($logSaver->context['server']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testExceptionHandlerUsesPreviousException()
    {
        $tracker = new \stdClass();
        $tracker->called = false;
        set_exception_handler(function($exception) use ($tracker) {
            $tracker->called = true;
        });
        
        $handler = new Handler(new NullLogger());
        try {
            $handler->handleException(new \Exception());
        }
        catch (\Exception $e) {
            // do nothing
        }
        $this->assertTrue($tracker->called);
    }
}