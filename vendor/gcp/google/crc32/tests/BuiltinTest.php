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

use Google\CRC32\Builtin;
use Google\CRC32\CRC32;

final class BuiltinTest extends TestCase
{
    public function testUnsupported()
    {
        $this->setExpectedException('InvalidArgumentException');

        new Builtin(CRC32::KOOPMAN);
    }

    /**
     * @dataProvider supports
     */
    public function testSupports($algo, $expected)
    {
        $this->assertEquals($expected, Builtin::supports($algo));
    }

    public function supports()
    {
        return [
            'IEEE' => [CRC32::IEEE, true],
            'CASTAGNOLI' => [CRC32::CASTAGNOLI, true],
            'KOOPMAN' => [CRC32::KOOPMAN, false],
        ];
    }
}
