<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata_Gbase
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Gbase.php';
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gbase
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gbase
 */
class Zend_Gdata_Gbase_ItemFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->itemFeed = new Zend_Gdata_Gbase_ItemFeed(
                file_get_contents(dirname(__FILE__) . '/_files/TestDataGbaseItemFeedSample1.xml'),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertEquals(2, count($this->itemFeed->entries));
        $this->assertEquals(2, $this->itemFeed->entries->count());
        foreach($this->itemFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
        }

        $newItemFeed = new Zend_Gdata_Gbase_ItemFeed();
        $doc = new DOMDocument();
        $doc->loadXML($this->itemFeed->saveXML());
        $newItemFeed->transferFromDom($doc->documentElement);

        $this->assertEquals(2, count($newItemFeed->entries));
        $this->assertEquals(2, $newItemFeed->entries->count());
        foreach($newItemFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
        }
    }

}
