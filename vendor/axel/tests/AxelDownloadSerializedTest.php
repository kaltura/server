<?php

/*************************************************************************
 *
 * AxelDownloadSerializableTest tests to ensure Axel can be serialized and
 * unserialized
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

namespace Axel;

class AxelDownloadSerializableTest extends \TestFixture {


    public function testSerialize() {

        $download = new AxelDownload();
        $download->addDownloadParameters([
            'address'           => 'http://www.google.com',
            'filename'          => 'test.html',
            'download_path'     => './',
            'callback'          => function(AxelDownload $download, $status, $success, $error) {
                $callback = 'Called back:' . print_r(array($download, $status, $success, $error), true);
                $this->assertContains('Called back', $callback);
                $this->assertTrue($success);
                $download->clearCompleted();
            }
        ]);
        $json = $download->serialize();

        $this->assertNotEmpty($json);
        $this->assertContains('Called back', $json);

        return $json;
    }

    /**
     * @depends testSerialize
     */
    public function testUnserialize($json) {

        $axel = new AxelDownload();
        $axel->unserialize($json);

        $this->assertNotEmpty($axel->getFullPath());
        $this->assertFileNotExists($axel->getFullPath());

        $axel->start();

        $this->assertFileExists($axel->getFullPath());
        $this->assertFileNotExists($axel->log_path);

        unlink($axel->getFullPath());
        $this->assertFileNotExists($axel->getFullPath());
    }
}