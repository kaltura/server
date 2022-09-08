<?php

/*************************************************************************
 *
 * AxelDownloadSerializable trait adds serializable functionality to
 * AxelDownload subclasses
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

use SuperClosure\Serializer;

trait AxelDownloadSerializable {

    protected $_serializedClosures = [];

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize() {
        return [
            'axel_path'      => $this->axel_path,
            'address'        => $this->address,
            'filename'       => $this->filename,
            'download_path'  => $this->download_path,
            'detach'         => $this->detach,
            'connections'    => $this->connections,
            'log_path'       => $this->log_path,
            'process_info'   => $this->process_info,
            'error'          => $this->error,
            'last_command'   => $this->last_command
        ];
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize() {
        return json_encode([
            'core'      => $this->jsonSerialize(),
            'callbacks' => $this->serializeClosures()
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized) {

        $array = json_decode($serialized, true);

        array_walk($array['core'], function($value, $key) {
            $this->$key = $value;
        });

        array_walk($array['callbacks'], function($closure, $key) {

            if (empty($this->_serializedClosures[$key])) {

                $this->callbacks[$key] = (new Serializer)->unserialize($closure);
                $this->_serializedClosures[$key] = $closure;
            }
        });
    }

    /**
     * Serializes closures that have not been previously serialized.
     *
     * @return array Serialized closures
     */
    protected function serializeClosures() {

        foreach ((array)$this->callbacks as $key=>$closure) {

            if (empty($this->_serializedClosures[$key])) {
                $this->_serializedClosures[$key] = (new Serializer)->serialize($closure);
            }
        }

        return $this->_serializedClosures;
    }
}