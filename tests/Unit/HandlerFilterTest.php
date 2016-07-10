<?php
/**
 * Test the handler filter
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHProblemLogger\Tests\Unit;

use AaronSaray\PHProblemLogger\HandlerFilter;

/**
 * Class HandlerFilterTest
 * @package AaronSaray\PHProblemLogger\Tests\Unit
 */
class HandlerFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testAllFilter()
    {
        $filter = HandlerFilter::all();
        $this->assertTrue(is_callable($filter));
        $this->assertEquals(['item'], $filter(['item']));
    }
    
    public function testNoneFilter()
    {
        $filter = HandlerFilter::none();
        $this->assertTrue(is_callable($filter));
        $this->assertNull($filter(['item']));
    }
}