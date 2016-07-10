<?php
/**
 * This is a logger used to make sure we can validate how logs have actually been sent
 * 
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger\Tests;

use Psr\Log\AbstractLogger;

/**
 * Class LogSaver
 * @package AaronSaray\PHProblemLogger\Tests
 */
class LogSaver extends AbstractLogger
{
    /**
     * @var mixed level
     */
    public $level;

    /**
     * @var mixed message
     */
    public $message;

    /**
     * @var array context
     */
    public $context;

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function log($level, $message, array $context = array())
    {
        $this->level = $level;
        $this->message  = $message;
        $this->context = $context;
    }
}