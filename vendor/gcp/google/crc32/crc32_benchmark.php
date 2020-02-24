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

include __DIR__ . '/vendor/autoload.php';

use Google\CRC32\Builtin;
use Google\CRC32\CRC32;
use Google\CRC32\Google;
use Google\CRC32\PHP;
use Google\CRC32\PHPSlicedBy4;

define('min_duration', 5);       // Min duration of test in seconds.
define('max_duration', 30);      // Max duration of test in seconds.
define('min_iterations', 10000); // Min number of iterations.


/*
Tested on my mid-2014 MacBook Pro (with SSE4.2)

Google CRC Benchmarks
CRC32CBenchmark/Public/256               214 ns          213 ns      3152131 bytes_per_second=1.11688G/s
CRC32CBenchmark/Public/4096             1975 ns         1974 ns       346883 bytes_per_second=1.9328G/s
CRC32CBenchmark/Public/65536           31805 ns        31782 ns        21701 bytes_per_second=1.92044G/s
CRC32CBenchmark/Public/1048576        508704 ns       508373 ns         1312 bytes_per_second=1.92096G/s
CRC32CBenchmark/Public/16777216      8526064 ns      8516872 ns           78 bytes_per_second=1.83459G/s
CRC32CBenchmark/Portable/256             363 ns          363 ns      1900480 bytes_per_second=672.139M/s
CRC32CBenchmark/Portable/4096           4610 ns         4607 ns       150221 bytes_per_second=847.9M/s
CRC32CBenchmark/Portable/65536         72886 ns        72815 ns         9168 bytes_per_second=858.341M/s
CRC32CBenchmark/Portable/1048576     1151197 ns      1150417 ns          585 bytes_per_second=869.25M/s
CRC32CBenchmark/Portable/16777216   18655381 ns     18640083 ns           36 bytes_per_second=858.365M/s
CRC32CBenchmark/Sse42/256                211 ns          211 ns      3245158 bytes_per_second=1.13004G/s
CRC32CBenchmark/Sse42/4096              1959 ns         1958 ns       347583 bytes_per_second=1.94816G/s
CRC32CBenchmark/Sse42/65536            32041 ns        32013 ns        21616 bytes_per_second=1.90658G/s
CRC32CBenchmark/Sse42/1048576         514282 ns       514035 ns         1296 bytes_per_second=1.8998G/s
CRC32CBenchmark/Sse42/16777216       8437749 ns      8433051 ns           78 bytes_per_second=1.85283G/s

CRC32_PHP              256       1500      11.48 MB/s
CRC32_Builtin          256       1500     315.81 MB/s
CRC32C_Google          256       1500    1078.78 MB/s
CRC32_PHP             4096       1500      11.65 MB/s
CRC32_Builtin         4096       1500     457.41 MB/s
CRC32C_Google         4096       1500   10836.76 MB/s
CRC32_PHP          1048576        118      12.27 MB/s
CRC32_Builtin      1048576       1500     468.74 MB/s
CRC32C_Google      1048576       1500   24684.46 MB/s
CRC32_PHP         16777216          8      12.24 MB/s
CRC32_Builtin     16777216        276     461.51 MB/s
CRC32C_Google     16777216       1500   20221.71 MB/s

*/

function test($crc, $chunk_size)
{
    //xdebug_start_trace();
    $name = get_class($crc);
    $chunk = random_bytes($chunk_size); // TODO for php 5 use https://github.com/paragonie/random_compat

    $i = 0;
    $now = microtime(true);
    $start = $now;
    $duration = 0;

    while (true) {
        $crc->update($chunk);

        $i++;
        $now = microtime(true);
        $duration = ($now - $start);

        if ($duration >= max_duration) {
            break;
        }
        if ($duration >= min_duration && $i >= min_iterations) {
            break;
        }
    }

    // Very quick sanity check
    if ($crc->hash() == '00000000') {
        exit($name . ' crc check failed');
    }


    $bytes = $i * $chunk_size;

    echo sprintf("%s\t%10d\t%5d\t%8.2f MB/s\n", $name, $chunk_size, $i, $bytes / ($now - $start) / 1000000);
}

foreach (array(256, 4096, 1048576, 16777216) as $chunk_size) {
    test(new PHP(CRC32::CASTAGNOLI), $chunk_size);
    test(new PHPSlicedBy4(CRC32::CASTAGNOLI), $chunk_size);

    // Using IEEE, avoiding the CASTAGNOLI version crc32c.so adds.
    test(new Builtin(CRC32::IEEE), $chunk_size);
    test(new Google(), $chunk_size);
}
