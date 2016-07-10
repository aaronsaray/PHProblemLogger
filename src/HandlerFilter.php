<?php
/**
 * Built in filter helpers
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger;

/**
 * Class HandlerFilter
 * @package AaronSaray\PHProblemLogger
 */
class HandlerFilter
{
    /**
     * Get a filter for all
     * 
     * @return \Closure
     */
    public static function all()
    {
        return function(array $payload) {
            return $payload;
        };
    }

    /**
     * Get a filter for none
     * 
     * @return \Closure
     */
    public static function none()
    {
        return function(array $payload) {
            return null;
        };
    }
}