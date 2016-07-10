<?php
/**
 * Main Handler file
 * 
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger;

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
}