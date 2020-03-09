--TEST--
crc32c() errors test
--SKIPIF--
<?php
if (!extension_loaded('crc32c')) {
    echo 'skip';
}
?>
--FILE--
<?php

// The only valid 2nd argument is null
var_dump(crc32c('ABCDEFG', null));

var_dump(crc32c('ABCDEFG', ''));
var_dump(crc32c('ABCDEFG', 0));
var_dump(crc32c('ABCDEFG', 0x12345678));
var_dump(crc32c('ABCDEFG', '12345678'));

// TODO test no argument, or non-string argument.

?>
--EXPECTF--
string(4) ",wVe"

Warning: crc32c(): Supplied crc must be a 4 byte string in %s on line %d
bool(false)

Warning: crc32c(): Supplied crc must be a 4 byte string in %s on line %d
bool(false)

Warning: crc32c(): Supplied crc must be a 4 byte string in %s on line %d
bool(false)

Warning: crc32c(): Supplied crc must be a 4 byte string in %s on line %d
bool(false)