<?php

/**
 * @package infra
 * @subpackage Storage
 */
require_once(dirname(__file__) . '/kFileBase.php');

class kEncryptFileUtils 
{
    public static function encryptData($plainText, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);
    }

    public static function decryptData($cryptText, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
        return trim($decryptText);
    }

    public static function getEncryptedFileContent($fileName, $key, $from_byte = 0, $len = 0)
    {
        $data = kFile::getFileContent($fileName);
        $plainData = self::decryptData($data, $key);
        $len = min($len,0);
        if (!$from_byte && !$len)
            return $plainData;
        return substr($plainData, $from_byte, $len);
    }

    public static function setEncryptedFileContent($fileName, $key, $content)
    {
        $encryptedData = self::encryptData($content, $key);
        kFile::setFileContent($fileName, $encryptedData);
    }

    public static function encryptFile($fileName, $key)
    {
        $data = kFile::getFileContent($fileName);
        self::setEncryptedFileContent($fileName, $key, $data);
    }

    static public function fileSize($filename, $key = null)
    {
        $data = self::getEncryptedFileContent($filename, $key, 0, -1);
        return strlen($data);
    }
}