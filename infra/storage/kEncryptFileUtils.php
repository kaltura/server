<?php

/**
 * @package infra
 * @subpackage Storage
 */
require_once(dirname(__file__) . '/kFileBase.php');

class kEncryptFileUtils
{
    //iv length should be 16
    CONST ENCRYPT_METHOD = "AES-256-CBC";
    const OPENSSL_RAW_DATA = 1;
    public static function encryptData($plainText, $key, $iv)
    {
        $iv = substr($iv,0, openssl_cipher_iv_length(self::ENCRYPT_METHOD));
        $encryptedData =  openssl_encrypt($plainText, self::ENCRYPT_METHOD, $key, self::OPENSSL_RAW_DATA , $iv);
        return base64_encode($encryptedData);
    }

    public static function decryptData($cipherText, $key, $iv)
    {
        $cipherText = base64_decode($cipherText);
        $iv = substr($iv,0, openssl_cipher_iv_length(self::ENCRYPT_METHOD));
        return openssl_decrypt($cipherText, self::ENCRYPT_METHOD, $key, self::OPENSSL_RAW_DATA, $iv);
    }

    public static function getEncryptedFileContent($fileName, $key = null, $iv = null, $from_byte = 0, $len = 0)
    {
        if (!$key)
            return kFileBase::getFileContent($fileName, $from_byte, $from_byte + $len);

        $data = kFileBase::getFileContent($fileName);
        $plainData = self::decryptData($data, $key, $iv);
        $len = max($len,0);
        if (!$from_byte && !$len)
            return $plainData;
        return substr($plainData, $from_byte, $len);
    }

    public static function setEncryptedFileContent($fileName, $key, $iv, $content)
    {
        $encryptedData = self::encryptData($content, $key, $iv);
        kFileBase::setFileContent($fileName, $encryptedData);
    }

    public static function encryptFile($fileName, $key, $iv)
    {
        $data = kFileBase::getFileContent($fileName);
        self::setEncryptedFileContent($fileName, $key, $iv, $data);
        return true;
    }

    public static function fileSize($filename, $key = null, $iv = null)
    {
        if (!$key)
            return kFileBase::fileSize($filename);
        $data = self::getEncryptedFileContent($filename, $key, $iv, 0, -1);
        return strlen($data);
    }

    
}