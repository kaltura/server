--TEST--
crc32c() hash tests
--SKIPIF--
<?php
if (!extension_loaded('crc32c')) {
    echo 'skip';
}
?>
--FILE--
<?php

var_dump(in_array('crc32c', hash_algos()));

$crc = hash_init('crc32c');
hash_update($crc, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghi');
hash_update($crc, 'jklmnopqrstuvwxyz0123456789');
echo hash_final($crc);

?>
--EXPECT--
bool(true)
a245d57d