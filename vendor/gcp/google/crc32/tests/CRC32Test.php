<?php
/**
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Google\CRC32\CRC32;

require 'DataIterator.php';

final class CRC32Test extends TestCase
{
    public function crcs()
    {
        return new CRCIterator();
    }

    public function data()
    {
        return new DataIterator();
    }

    /**
     * @dataProvider data
     */
    public function testHash($crc_class, $poly, $input, $expected)
    {
        $crc = new $crc_class($poly);
        $crc->update($input);

        $this->assertEquals(
            $expected,
            $crc->hash(false),
            'hash(' . $input . ')'
        );

        $this->assertEquals(
            $expected,
            $crc->hash(false),
            'hash(' . $input . ') again'
        );
    }

    /**
     * @dataProvider data
     */
    public function testHashRaw($crc_class, $poly, $input, $expected)
    {
        $crc = new $crc_class($poly);
        $crc->update($input);

        $this->assertEquals(
            hex2bin($expected),
            $crc->hash(true),
            'hash(' . $input . ', true)'
        );
    }

    /**
     * Extended hashing. Read increasingly sized chunks of input.
     * @dataProvider data
     */
    public function testExtendedHash($crc_class, $poly, $input, $expected)
    {
        $crc = new $crc_class($poly);

        $start = 0;
        $len = 1;
        while ($start < strlen($input)) {
            $chunk = substr($input, $start, $len);
            $crc->update($chunk);

            $start += $len;
            $len *= 2;
        }

        $this->assertEquals(
            $expected,
            $crc->hash(),
            'hash(' . $input . ')'
        );
    }

    /**
     * @dataProvider crcs
     */
    public function testReset($crc_class, $poly)
    {
        $data = array(
            'abc' => array(
                CRC32::IEEE => '352441c2',
                CRC32::CASTAGNOLI => '364b3fb7'),
            'abcdef' => array(
                CRC32::IEEE => '4b8e39ef',
                CRC32::CASTAGNOLI => '53bceff1'),
        );

        $crc = new $crc_class($poly);
        $this->assertEquals('00000000', $crc->hash());

        $crc->update('abc');
        $this->assertEquals($data['abc'][$poly], $crc->hash());

        $crc->reset();
        $this->assertEquals('00000000', $crc->hash());

        $crc->update('abcdef');
        $this->assertEquals($data['abcdef'][$poly], $crc->hash());
    }

    /**
     * @dataProvider crcs
     */
    public function testClone($crc_class, $poly)
    {
        $data = array(
            'abc' => array(
                CRC32::IEEE => '352441c2',
                CRC32::CASTAGNOLI => '364b3fb7'),
            'abcdefgh' => array(
                CRC32::IEEE => 'aeef2a50',
                CRC32::CASTAGNOLI => '0a9421b7'),
        );

        $a = new $crc_class($poly);
        $a->update('abc');

        $b = clone $a;
        $b->update('defgh');

        // $b should be updated, but $a should stay the same.
        $this->assertEquals(
            $data['abc'][$poly],
            $a->hash(),
            '$a->update("abc")'
        );

        $this->assertEquals(
            $data['abcdefgh'][$poly],
            $b->hash(),
            'clone($a)->update("abcdefgh")'
        );
    }
}
