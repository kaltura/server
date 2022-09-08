<?php

/*************************************************************************
 *
 * AxelDownloadTest class runs PHPUnit tests of AxelDownload
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

class AxelDownloadTest extends \TestFixture {

    protected $short_address   = 'http://www.google.com';
    protected $long_address    = 'http://ipv4.download.thinkbroadband.com/1GB.zip';

    public function testAxelInstalled() {

        // Instance
        $axel = new AxelDownload();

        $this->assertTrue($axel->checkAxelInstalled());
    }

    /**
     * @depends testAxelInstalled
     */
    public function testStartDownloadAsync() {

        $download_address = $this->long_address;

        // Instance
        $this->assertFileNotExists(basename($download_address));
        $axel = new AxelDownload();
        $axel->startAsync($download_address);

        // Wait for download to initialise
        $this->setTimeout(10);

        // Tests
        $this->assertSame($axel->last_command, AxelDownload::STARTED);

        return $axel;
    }

    /**
     * @depends testStartDownloadAsync
     */
    public function testReadLogDownloadAsync(AxelDownload $axel) {

        $this->assertFileExists($axel->getFullPath());
        $this->assertFileExists($axel->getFullPath() . '.st');
        $this->assertFileExists($axel->log_path);

        $status = $axel->updateStatus();

        $this->assertTrue(is_array($status));
        $this->assertTrue(!empty($status));
        $this->assertTrue(count($status) == 3);
        $this->assertTrue(empty($axel->error));

        return $axel;
    }

    /**
     * @depends testReadLogDownloadAsync
     */
    public function testCancelDownloadAsync(AxelDownload $axel) {

        $this->assertFileExists($axel->getFullPath());
        $this->assertFileExists($axel->getFullPath() . '.st');
        $this->assertFileExists($axel->log_path);

        $axel->cancel();

        $this->assertFileNotExists($axel->getFullPath());
        $this->assertFileNotExists($axel->getFullPath() . '.st');
        $this->assertFileNotExists($axel->log_path);

        return $axel;
    }

    /**
     * @depends testAxelInstalled
     */
    public function testStartDownloadSync() {

        $download_address = $this->short_address;
        $axel = $this->setUpSyncDownload($download_address);
        $axel->start($download_address);

        $this->assertSuccessfulDownload($axel);

        return $axel;
    }

    /**
     * @depends testAxelInstalled
     */
    public function testStartDownloadWithOptionsSync() {

        $download_address = $this->short_address;
        $axel = $this->setUpSyncDownload($download_address);
        $axel->start($download_address, 'test.html', dirname(__DIR__ . '/../')); // Use current directory to test

        $this->assertSuccessfulDownload($axel);

        return $axel;
    }

    protected function setUpSyncDownload($download_address) {

        // Instance
        $this->assertFileNotExists(basename($download_address));
        $axel = new AxelDownload();
        $this->assertSame($axel->last_command, AxelDownload::CREATED);

        return $axel;
    }

    protected function assertSuccessfulDownload(AxelDownload $axel) {

        // Tests
        $this->assertSame($axel->last_command, AxelDownload::COMPLETED);
        $this->assertFileExists($axel->getFullPath());
        $contents = file_get_contents($axel->getFullPath());
        $this->assertContains('input', $contents);
        $this->assertTrue($axel->clearCompleted());
        $this->assertFileNotExists($axel->log_path);
        $this->assertFileExists($axel->getFullPath());
        $this->assertFileNotExists($axel->getFullPath() . '.st');
        unlink($axel->getFullPath());
        $this->assertFileNotExists($axel->getFullPath());

        return $axel;
    }

    /**
     * @depends testAxelInstalled
     */
    public function testStartDownloadSyncWithCallback() {

        $download_address = $this->short_address;

        $callback_called = false;

        // Instance
        $axel = new AxelDownload();
        $axel->start($download_address, null, null, function(AxelDownload $axel, $status, $success, $error) use ($download_address, &$callback_called) {

            $callback_called = true;

            // Tests
            $this->assertTrue($success);
            $this->assertSame($axel->last_command, AxelDownload::COMPLETED);
            $this->assertFileExists($axel->getFullPath());
            $contents = file_get_contents($axel->getFullPath());
            $this->assertContains('input', $contents);
            $this->assertTrue($axel->clearCompleted());
            $this->assertFileNotExists($axel->log_path);
            $this->assertFileExists($axel->getFullPath());
            $this->assertFileNotExists($axel->getFullPath() . '.st');
            unlink($axel->getFullPath());
            $this->assertFileNotExists($axel->getFullPath());
        });

        $this->assertTrue($callback_called);
    }

    /**
     * @depends testAxelInstalled
     */
    public function testStartDownloadASyncWithCallback() {

        $download_address = $this->long_address;

        $callback_count = 0;

        // Instance
        $axel = new AxelDownload();
        $axel->startAsync($download_address, null, null, function(AxelDownload $axel, $status, $success, $error) use ($download_address, &$callback_count) {

            $callback_count++;

            // Tests
            $this->assertFalse($success);
            $this->assertSame($axel->last_command, AxelDownload::STARTED);
            $this->assertFileExists($axel->getFullPath());
            $this->assertFileExists($axel->getFullPath() . '.st');
            $this->assertSame(3, count($status));
            $this->assertEmpty($error);
        });

        $this->setTimeout(10);

        for ($i = 0; $i < 2; $i++) {
            $this->setTimeout(5);

            $axel->updateStatus();
        }

        $this->assertSame(2, $callback_count);
        $this->testCancelDownloadAsync($axel);
    }
}