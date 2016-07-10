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
     * Return payload 
     * 
     * @param array $payload
     * @return array
     */
    public static function all(array $payload)
    {
        return $payload;
    }

    /**
     * Get no payload
     * 
     * @param array $payload
     * @return null
     */
    public static function none(array $payload)
    {
        return null;
    }
}