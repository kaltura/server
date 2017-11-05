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
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedData =  openssl_encrypt($plainText, self::ENCRYPT_METHOD, $key, 0 , $iv);
        return base64_encode($iv.$encryptedData);
    }

    public static function decryptData($cipherText, $key)
    {
        $cipherText = base64_decode($cipherText);
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $iv = substr($cipherText, 0, $ivLength);
        return openssl_decrypt(substr($cipherText, $ivLength), self::ENCRYPT_METHOD, $key, 0, $iv);
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