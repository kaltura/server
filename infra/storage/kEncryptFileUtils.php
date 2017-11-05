<?php

/**
 * @package infra
 * @subpackage Storage
 */
require_once(dirname(__file__) . '/kFileBase.php');

class kEncryptFileUtils
{
    CONST ENCRYPT_METHOD = "AES-256-CBC";
    public static function encryptData($plainText, $key)
    {
        return openssl_encrypt($plainText, self::ENCRYPT_METHOD, $key);
    }

    public static function decryptData($cryptText, $key)
    {
        return openssl_decrypt($cryptText, self::ENCRYPT_METHOD, $key);
    }

    public static function getEncryptedFileContent($fileName, $key = null, $from_byte = 0, $len = 0)
    {
        if (!$key)
            return kFileBase::getFileContent($fileName, $from_byte, $len);
        $data = kFileBase::getFileContent($fileName);
        $plainData = self::decryptData($data, $key);
        $len = min($len,0);
        if (!$from_byte && !$len)
            return $plainData;
        return substr($plainData, $from_byte, $len);
    }

    public static function setEncryptedFileContent($fileName, $key, $content)
    {
        $encryptedData = self::encryptData($content, $key);
        kFileBase::setFileContent($fileName, $encryptedData);
    }

    public static function encryptFile($fileName, $key)
    {
        $data = kFileBase::getFileContent($fileName);
        self::setEncryptedFileContent($fileName, $key, $data);
    }

    static public function fileSize($filename, $key = null)
    {
        if (!$key)
            return kFileBase::fileSize($filename);
        $data = self::getEncryptedFileContent($filename, $key, 0, -1);
        return strlen($data);
    }
}