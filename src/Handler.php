<?php
/**
 * Main Handler file
 * 
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger;
use Psr\Log\LoggerInterface;

/**
 * Class Handler
 * @package AaronSaray\PHProblemLogger
 */
class Handler
{
    /**
     * @var array values store for logging
     */
    protected $values = [
        'session'       =>  null,
        'get'           =>  null,
        'post'          =>  null,
        'cookie'        =>  null,
        'environment'   =>  null,
        'server'        =>  null,
        'application'   =>  null
    ];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Callable|Null
     */
    protected $previousExceptionHandler;

    /**
     * Handler constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->registerExceptionHandler();
        $this->registerErrorHandler();
    }

    /**
     * Session Values
     * 
     * @param callable $callable
     * @return $this
     */
    public function session(callable $callable)
    {
        $this->values['session'] = $callable($_SESSION);
        return $this;
    }

    /**
     * GET Values
     *
     * @param callable $callable
     * @return $this
     */
    public function get(callable $callable)
    {
        $this->values['get'] = $callable($_GET);
        return $this;
    }

    /**
     * POST Values
     *
     * @param callable $callable
     * @return $this
     */
    public function post(callable $callable)
    {
        $this->values['post'] = $callable($_POST);
        return $this;
    }

    /**
     * Cookie Values
     *
     * @param callable $callable
     * @return $this
     */
    public function cookie(callable $callable)
    {
        $this->values['cookie'] = $callable($_COOKIE);
        return $this;
    }

    /**
     * Environment Values
     *
     * @param callable $callable
     * @return $this
     */
    public function environment(callable $callable)
    {
        $this->values['environment'] = $callable($_ENV);
        return $this;
    }

    /**
     * Server Values
     *
     * @param callable $callable
     * @return $this
     */
    public function server(callable $callable)
    {
        $this->values['server'] = $callable($_SERVER);
        return $this;
    }

    /**
     * Custom application values
     * 
     * @param callable $callable
     * @return $this
     */
    public function application(callable $callable)
    {
        $this->values['application'] = $callable([]);
        return $this;
    }

    /**
     * Register the exception handler
     */
    protected function registerExceptionHandler()
    {
        $this->previousExceptionHandler = set_exception_handler(array($this, 'handleException'));
    }

    /**
     * Exception Handler
     * 
     * This filters our log items, logs our exception, and then if there was a previous exception handler, does that one
     *
     * @note PHP7 breaks \Exception as Exception type - and goes with \Throwable because of the Error object
     * @param $exception \Exception|\Throwable
     */
    public function handleException($exception)
    {
        $logItems = array_filter($this->values, function($values) {
            return !is_null($values);
        });
        $this->logger->error($exception, $logItems);

        if (is_callable($this->previousExceptionHandler)) {
            $callable = $this->previousExceptionHandler;
            $callable($exception);
        }
    }

    /**
     * Register the error handler
     */
    protected function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
    }

    /**
     * Handles the error into an exception
     * 
     * @param $errorNumberSeverity integer
     * @param $errorString string
     * @param $errorFile string
     * @param $errorLine integer
     * @param $errorContext array
     * @throws \ErrorException
     */
    public function handleError($errorNumberSeverity, $errorString, $errorFile, $errorLine, $errorContext)
    {
        if (!(error_reporting() & $errorNumberSeverity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new \ErrorException($errorString, 0, $errorNumberSeverity, $errorFile, $errorLine);
    }
}