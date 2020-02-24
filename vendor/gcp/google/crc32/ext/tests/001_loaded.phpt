--TEST--
crc32c() is loadable
--SKIPIF--
<?php
if (!extension_loaded('crc32c')) {
    echo 'skip';
}
?>
--FILE--
<?php
echo 'The extension "crc32c" is available';
?>
--EXPECT--
The extension "crc32c" is available
