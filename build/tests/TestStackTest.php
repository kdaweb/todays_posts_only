<?php
/**
 * This is a starter unit test -- it doesn't accomplish much (anything)
 *
 * This is a starter unit test.  This test only tests PHP stack-related
 * methods; if something here fails, there's a serious problem with your
 * PHP implementation, not your code.
 *
 * PHP Version 5.2.4
 * @category   TodaysPostsOnly
 * @package    TodaysPostsOnly
 * @author     KDA Web Technologies, Inc. <info@kdaweb.com>
 * @copyright  2017 KDA Web Technologies, Inc.
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    GIT: $Id$
 * @link       http://kdaweb.com/ KDA Web Technologies, Inc.
 *
 */

// for PHPUnnit >= 5.3
//use PHPUnit\Framework\TestCase;

//class StackTest extends TestCase {


// for PHPUnit < 5.3
require_once 'PHPUnit/Autoload.php';

/**
 * this class extends the PHPUnit framework test case
 *
 * @category StackTest
 * @package  StackTest
 * @author   KDA Web Technologies, Inc. <info@kdaweb.com>
 * @copyright  2017 KDA Web Technologies, Inc.
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://kdaweb.com/ KDA Web Technologies, Inc.
 */
class StackTest extends PHPUnit_Framework_Testcase {


  /**
 * test stack push and pop functions
 *
 * @return null
 */
  public function testPushAndPop() {
      $stack = [];
      $this->assertEquals(0, count($stack));

      array_push($stack, 'foo');
      $this->assertEquals('foo', $stack[count($stack)-1]);
      $this->assertEquals(1, count($stack));

      $this->assertEquals('foo', array_pop($stack));
      $this->assertEquals(0, count($stack));
  }


}
?>
