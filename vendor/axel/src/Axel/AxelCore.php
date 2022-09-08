<?php

/*************************************************************************
 *
 * AxelCore class handles interaction with the system using Symfony.
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

use Symfony\Component\Process\Process;

class AxelCore {

    /**
     * @var string Full path to Axel binary
     * @example '/usr/bin/axel'
     */
    protected $axel_path;

    /**
     * @var bool To perform Async downloads set to true
     */
    protected $detach         = false;

    /**
     * @var int The number of connections to attempt to use to download the file
     */
    protected $connections    = 10;

    /**
     * @var array Array containing process information if the process is running.
     * @example May contain ['pid' => 1234]
     */
    protected $process_info;

    /**
     * @var string The last error encountered
     */
    public  $error;

    /**
     * Class constructor
     *
     * @param string $axel_path Full path to Axel binary
     * @param int $connections The number of connections to attempt to use to download the file
     */
    public function __construct($axel_path = 'axel', $connections = 10) {

        $this->axel_path            = (is_string($axel_path) && !empty($axel_path))         ? $axel_path : 'axel';
        $this->connections          = (is_int($connections) && $connections >= 1)           ? $connections : 10;
    }

    /**
     * Check if the specified Axel binary is installed / callable
     *
     * @return bool Whether Axel is installed
     */
    public function checkAxelInstalled() {
        $process = new Process($this->axel_path . ' --version');

        $process->run();
        if (!$process->isSuccessful()) {
            $this->error = $process->getErrorOutput();

            return false;
        }
        else {

            return true;
        }
    }

    /**
     * Executes the download
     *
     * @param string $command The download command
     * @param string $command_args Optional arguments
     * @param bool $process_info Whether to fetch and set process information
     * @return bool If the command executed successfully
     */
    protected function execute($command, $command_args, $process_info = true) {

        $detach = ($this->detach) ? ' 2>&1 &': '';
        $process = ($process_info) ? ' echo $!': '';

        // Spawn off the process
        $process = new Process($command . $command_args . $detach . $process);

        $process->run();
        if (!$process->isSuccessful()) {
            $this->error = $process->getErrorOutput();

            return false;
        }
        else {
            if ($process_info) {
                $this->process_info['pid'] = $process->getOutput();
            }

            return true;
        }
    }
}