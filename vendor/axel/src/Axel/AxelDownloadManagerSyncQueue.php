<?php

/*************************************************************************
 *
 * AxelDownloadManagerSyncQueue class runs jobs in the AxelDownloadManager
 * synchronously.
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

class AxelDownloadManagerSyncQueue implements AxelDownloadManagerQueueInterface {

    /**
     * @var AxelDownloadManager A reference to the created AxelDownloadManager to callback to when download has finished
     */
    private $axelDownloadManager;

    /**
     * Stores a reference to the created AxelDownloadManager to callback to when download has finished
     *
     * @param AxelDownloadManager $axelDownloadManager
     */
    public function setDownloadManager(AxelDownloadManager $axelDownloadManager) {
        $this->axelDownloadManager = $axelDownloadManager;
    }


    /**
     * Processes a download immediately and then notifies the handler it has been completed
     *
     * @param AxelDownload $download
     */
    public function addDownloadToQueue(AxelDownload $download) {
        $download->start();
        $this->axelDownloadManager->notifyCompletedDownload($download);
    }
}