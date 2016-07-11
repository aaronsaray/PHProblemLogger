<?php
/**
 * Error Exception is used to handle error logging.
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger;

/**
 * Class ErrorException
 * @package AaronSaray\PHProblemLogger
 */
class ErrorException extends \Exception
{
    /**
     * @param $file string file for the error
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param $line int the line of the file
     */
    public function setLine($line)
    {
        $this->line = $line;
    }
}