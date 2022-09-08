<?php

/*************************************************************************
 *
 * AxelDownloadManager class manages a series of downloads.
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

class AxelDownloadManager {

    /**
     * @var AxelDownloadManagerQueueInterface The queue that will process the downloads
     * @example AxelDownloadManagerSyncQueue
     */
    protected $queue;

    /**
     * @var string Full path to Axel binary
     * @example '/usr/bin/axel'
     */
    protected $path_to_axel;

    /**
     * @var bool Whether the queue should continue processing
     */
    public $processing                  = false;

    /**
     * @var array Queue containing currently running jobs
     */
    protected $running                  = [];

    /**
     * @var array Queue containing scheduled jobs to run
     */
    protected $scheduled                = [];

    /**
     * @var int Allowed number of concurrent downloads
     */
    protected $concurrent_downloads     = 1;

    /**
     * @var int Allowed number of concurrent connections per download
     */
    protected $concurrent_connections   = 1;

    /**
     * @var array Array of completed jobs
     */
    public $completed                   = [];

    /**
     * @param AxelDownloadManagerQueueInterface $queue Instance of AxelDownloadManagerQueueInterface to callback to
     * @param null|string $path_to_axel Full path to Axel binary
     * @param int $concurrent_downloads Allowed number of concurrent downloads
     * @param int $concurrent_connections Allowed number of concurrent connections per download
     */
    public function __construct(AxelDownloadManagerQueueInterface $queue, $path_to_axel = null, $concurrent_downloads = 1, $concurrent_connections = 10) {

        $this->queue                    = $queue;
        $this->queue->setDownloadManager($this);
        $this->path_to_axel             = (is_string($path_to_axel))? $path_to_axel : 'axel';
        $this->concurrent_downloads     = (is_int($concurrent_downloads) && $concurrent_downloads >= 0)? $concurrent_downloads : 1;
        $this->concurrent_connections   = (is_int($concurrent_connections) && $concurrent_connections >= 0)? $concurrent_connections : 10;
    }

    /**
     * Create a download and enqueue it on the download queue
     *
     * @param string $address File to download
     * @param null|string $filename Filename to save the downloaded file with
     * @param null|string $download_path Path to save the downloaded file at
     * @return AxelDownload The AxelDownload object that was queued
     */
    public function queueDownload($address, $filename = null, $download_path = null) {

        $download = new AxelDownload($this->path_to_axel, $this->concurrent_connections);
        $download->addDownloadParameters([
            'address'           => $address,
            'filename'          => $filename,
            'download_path'     => $download_path
        ]);
        $this->enqueueDownload($download);

        return $download;
    }

    /**
     * Adds an AxelDownload instance to the download queue
     *
     * @param AxelDownload $download The download to queue
     */
    public function enqueueDownload(AxelDownload $download) {
        array_push($this->scheduled, $download);
    }

    /**
     * Processes remaining jobs in the queue
     */
    public function processQueue() {

        if (count($this->scheduled) > 0) {

            $this->processing = true;

            while((count($this->running) < $this->concurrent_downloads) && !empty($this->scheduled)) {

                $download = array_shift($this->scheduled);

                if ($download instanceof AxelDownload) {
                    array_push($this->running, $download);
                    $this->queue->addDownloadToQueue($download);
                }
            }
        }
        else {
            //At present, queue will stop running when no more jobs have been added to it
            $this->processing = false;
        }
    }

    /**
     * Callback that AxelDownloadManagerQueues should message on complete download
     *
     * @param AxelDownload $download The download that completed
     */
    public function notifyCompletedDownload(AxelDownload $download) {

        if (!empty($this->running)) {

            $array_key = null;

            foreach ($this->running as $key => $job) {

                if ($job === $download) { // Todo check this

                    $download->clearCompleted();
                    array_push($this->completed, $download);
                    $array_key = $key;
                    break;
                }
            }

            if (!empty($array_key)) {
                unset($this->running[$array_key]);
            }
        }

        // Add next jobs to the queue if the queue is still active
        if ($this->processing) {
            $this->processQueue();
        }
    }
}