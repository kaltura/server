<?php

/*************************************************************************
 *
 * AxelTest class runs PHPUnit tests of Axel
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

class AxelDownloadManagerTest extends \TestFixture {

    public function testSyncDownloadWithJobs($count = 2) {

        $dm = new AxelDownloadManager(new AxelDownloadManagerSyncQueue(), 'axel', $count);

        $this->assertInstanceOf('Axel\AxelDownloadManager', $dm);

        for ($i = 0; $i < $count; $i++ ) {

            $download = $dm->queueDownload('http://www.google.com', 'file' . $i . '.html');
            $this->assertFileNotExists($download->getFullPath());
        }

        unset($download);
        $dm->processQueue();

        for ($i = 0; $i < $count; $i++ ) {

            $this->assertArrayHasKey($i, $dm->completed);
            $download = $dm->completed[$i];
            $this->assertFileExists($download->getFullPath());
            $this->assertFileNotExists($download->log_path);
            unlink($download->getFullPath());
        }
    }
}