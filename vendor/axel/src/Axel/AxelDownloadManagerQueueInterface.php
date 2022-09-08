<?php

/*************************************************************************
 *
 * AxelDownloadManagerQueueInterface interface describes an interactive
 * queue that download jobs can be pushed to.
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

interface AxelDownloadManagerQueueInterface {

    /**
     * Allows Queues to keep a reference of the download manager to call back to when a download has completed.
     *
     * @param AxelDownloadManager $axelDownloadManager The download manager object to reference
     * @return void
     */
    public function setDownloadManager(AxelDownloadManager $axelDownloadManager);

    /**
     * An opportunity for the queue to handle the download as it sees fit.
     * This could be to download the file immediately, to schedule it on a background thread, or some other task.
     *
     * @param AxelDownload $download The download to process
     * @return void
     */
    public function addDownloadToQueue(AxelDownload $download);

}