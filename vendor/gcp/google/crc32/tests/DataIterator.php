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
use PHPUnit\Framework\TestCase;

use Google\CRC32\CRC32;

class CRCIterator implements Iterator
{
    protected $crcs = [
        'Google\CRC32\PHP',
        'Google\CRC32\PHPSlicedBy4',
        'Google\CRC32\Builtin',
        'Google\CRC32\Google',
    ];

    protected $algos = [
        CRC32::IEEE,
        CRC32::CASTAGNOLI
    ];

    protected $count = 0;

    public function rewind()
    {
        reset($this->crcs);
        reset($this->algos);
    }

    public function valid()
    {
        return current($this->crcs) !== false &&
               current($this->algos) !== false;
    }

    public function key()
    {
        return $this->count;
    }

    public function current()
    {
        return [current($this->crcs), current($this->algos)];
    }

    public function next()
    {
        $this->count++;
        if (next($this->algos) === false) {
            reset($this->algos);
            next($this->crcs);
        }

        // Skip unsupported polynomials
        if ($this->valid()) {
            $crc_class = current($this->crcs);
            $poly = current($this->algos);
            if (!$crc_class::supports($poly)) {
                $this->next();
            }
        }
    }
}

class DataIterator implements Iterator
{
    protected $crcs = [
        'Google\CRC32\PHP',
        'Google\CRC32\PHPSlicedBy4',
        'Google\CRC32\Builtin',
        'Google\CRC32\Google',
    ];

    protected $algos = [
        CRC32::IEEE,
        CRC32::CASTAGNOLI
    ];

    /**
     * Various test data, taken from:
     *  * https://github.com/php/php-src/blob/master/ext/hash/tests/crc32.phpt
     *  * https://golang.org/src/hash/crc32/crc32_test.go
     *  * https://tools.ietf.org/html/rfc3720#appendix-B.4
     *
     * @var        array  Hashes for CRC32::IEEE and CRC32::CASTAGNOLI
     */
    protected $data = [
        '' => array('00000000', '00000000'),
        'a' => array('e8b7be43', 'c1d04330'),
        'ab' => array('9e83486d', 'e2a22936'),
        'abc' => array('352441c2', '364b3fb7'),
        'abcd' => array('ed82cd11', '92c80a31'),
        'abcde' => array('8587d865', 'c450d697'),
        'abcdef' => array('4b8e39ef', '53bceff1'),
        'abcdefg' => array('312a6aa6', 'e627f441'),
        'abcdefgh' => array('aeef2a50', '0a9421b7'),
        'abcdefghi' => array('8da988af', '2ddc99fc'),
        'abcdefghij' => array('3981703a', 'e6599437'),
        'abcdefghijklmnopqrstuvwxyz' => array('4c2750bd', '9ee6ef25'),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' => array('1fc2e6d2', 'a245d57d'),
        '12345678901234567890123456789012345678901234567890123456789012345678901234567890' => array('7ca94a72', '477a6781'),
        'message digest' => array('20159d7f', '02bd79d0'),
        "I can't remember anything" => array('69147a4e', '5e405e93'),
        "I can't remember anythingCan’t tell if this is true or dream" => array('3ee63999', '516ad412'),
        'Discard medicine more than two years old.' => array('6b9cdfe7', 'b2cc01fe'),
        'He who has a shady past knows that nice guys finish last.' => array('c90ef73f', '0e28207f'),
        "I wouldn't marry him with a ten foot pole." => array('b902341f', 'be93f964'),
        "Free! Free!/A trip/to Mars/for 900/empty jars/Burma Shave" => array('042080e8', '9e3be0c3'),
        "The days of the digital watch are numbered.  -Tom Stoppard" => array('154c6d11', 'f505ef04'),
        "Nepal premier won't resign." => array('4c418325', '85d3dc82'),
        "For every action there is an equal and opposite government program." => array('33955150', 'c5142380'),
        "His money is twice tainted: 'taint yours and 'taint mine." => array('26216a4b', '75eb77dd'),
        "There is no reason for any individual to have a computer in their home. -Ken Olsen, 1977" => array('1abbe45e', '91ebe9f7'),
        "It's a tiny change to the code and not completely disgusting. - Bob Manchek" => array('c89a94f7', 'f0b1168e'),
        "size:  a.out:  bad magic" => array('ab3abe14', '572b74e2'),
        "The major problem is with sendmail.  -Mark Horton" => array('bab102b6', '8a58a6d5'),
        "Give me a rock, paper and scissors and I will move the world.  CCFestoon" => array('999149d7', '9c426c50'),
        "If the enemy is within range, then so are you." => array('6d52a33c', '735400a4'),
        "It's well we cannot hear the screams/That we create in others' dreams." => array('90631e8d', 'bec49c95'),
        "You remind me of a TV show, but that's all right: I watch it anyway." => array('78309130', 'a95a2079'),
        "C is as portable as Stonehedge!!" => array('7d0a377f', 'de2e65c5'),
        "Even if I could be Shakespeare, I think I should still choose to be Faraday. - A. Huxley" => array('8c79fd79', '297a88ed'),
        "The fugacity of a constituent in a mixture of gases at a given temperature is proportional to its mole fraction.  Lewis-Randall Rule" => array('a20b7167', '66ed1d8b'),
        "How can you write a big system without C++?  -Paul Glick" => array('8e0bb443', 'dcded527'),
        "\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f !\"#\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff" => array('29058c73', '9c44184b')
    ];

    protected $count = 0;

    public function rewind()
    {
        reset($this->crcs);
        reset($this->algos);
        reset($this->data);
    }

    public function valid()
    {
        return current($this->crcs) !== false &&
               current($this->algos) !== false &&
               current($this->data) !== false;
    }

    public function key()
    {
        return $this->count;
    }

    public function current()
    {
        return [current($this->crcs), current($this->algos), key($this->data), current($this->data)[key($this->algos)]];
    }

    public function next()
    {
        $this->count++;
        if (next($this->data) === false) {
            reset($this->data);
            if (next($this->algos) === false) {
                reset($this->algos);
                next($this->crcs);
            }
        }

        // Skip unsupported polynomials
        if ($this->valid()) {
            $crc_class = current($this->crcs);
            $poly = current($this->algos);
            if (!$crc_class::supports($poly)) {
                $this->next();
            }
        }
    }
}
