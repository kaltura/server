--TEST--
crc32c() extend tests
--SKIPIF--
<?php
if (!extension_loaded('crc32c')) {
    echo 'skip';
}
?>
--FILE--
<?php

$crc = crc32c('ABCDEFG', hex2bin('00000000'));
echo bin2hex($crc), "\n";

$crc = null;

$crc = crc32c('ABCDEFG', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('HIJKLMNOP', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('QRSTUVWXYZ', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('abcdefghijklmno', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('pqrstuvwxyz0', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('123456789', $crc);
echo bin2hex($crc), "\n";

$crc = crc32c('', $crc);
echo bin2hex($crc), "\n";

?>
--EXPECT--
2c775665
2c775665
5e2b5be5
319897cd
2f6298bc
86bd0651
a245d57d
a245d57d