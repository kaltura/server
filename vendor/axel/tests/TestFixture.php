<?php

/*************************************************************************
 *
 * TestFixture class sets up a TestFixture for PHPUnit tests to extend.
 *
 * =======================================================================
 *
 * This file is part of the Axel package.
 *
 * @author (c) Ian Outterside <ian@ianbuildsapps.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

use Axel\Axel;

class TestFixture extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {

    }

    protected function tearDown()
    {
    }


    protected function log($output, $message = null) {

        if(in_array('--debug', $_SERVER['argv'], true)) {
            fwrite(STDERR, '========================' . "\n");
            if (is_string($message)) {
                fwrite(STDERR, $message . "\n\n");
            }

            fwrite(STDERR, print_r($output, TRUE));
            fwrite(STDERR, '========================' . "\n\n");
        }
    }

    protected function setTimeout($time) {
        sleep($time);
    }
}
