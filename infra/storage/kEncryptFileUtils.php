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
    const OPENSSL_RAW_DATA = 1; //can be removed once on PHP7
    const ENCRYPT_INTERVAL = 3145728; // as 3MB = 1024 * 1024 * 3
    const AES_BLOCK_SIZE = 16; //For IV extraction

    public static function encryptData($plainText, $key, $iv)
    {
        $iv = substr($iv,0, openssl_cipher_iv_length(self::ENCRYPT_METHOD));
        return openssl_encrypt($plainText, self::ENCRYPT_METHOD, $key, self::OPENSSL_RAW_DATA , $iv);
    }

    public static function decryptData($cipherText, $key, $iv)
    {
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

    private static function doEncryptFile($srcFd, $key, $iv, $destFd)
    {
        $clear = fread($srcFd,self::ENCRYPT_INTERVAL);
        $enc = self::encryptData($clear, $key, $iv);
        $iv = substr($enc, -self::AES_BLOCK_SIZE);
        fwrite($destFd, $enc);
        return $iv;
    }

    public static function encryptFile($srcFilePath, $key, $iv, $dstFilePath = null)
    {
        return self::wrapFileAccess('doEncryptFile', $srcFilePath, $key, $iv, $dstFilePath);
    }

    private static function doDecryptFile($srcFd, $key, $iv, $destFd)
    {
        $content = fread($srcFd, self::ENCRYPT_INTERVAL + self::AES_BLOCK_SIZE);
        $clear = self::decryptData($content, $key, $iv);
        fwrite($destFd, $clear);
        return substr($content, -self::AES_BLOCK_SIZE);
    }

    public static function decryptFile($srcFilePath, $key, $iv, $dstFilePath = null)
    {
        return self::wrapFileAccess('doDecryptFile', $srcFilePath, $key, $iv, $dstFilePath);
    }

    private static function wrapFileAccess($functionName, $srcFilePath, $key, $iv, $dstFilePath = null)
    {
        $fd1 = $fd2 = null;
        try
        {
            $tempPath =  self::getClearTempPath($srcFilePath);
            $fd1 = fopen($srcFilePath, "rb");
            $fd2 = fopen($tempPath, "w");
            while (!feof($fd1))
            {
                $iv = call_user_func_array("self::$functionName", array($fd1, $key, $iv, $fd2));
            }
            fclose($fd1);
            fclose($fd2);

            if (!$dstFilePath)
                $dstFilePath = $srcFilePath;
            return rename($tempPath, $dstFilePath);
        }
        catch(Exception $e)
        {
            if ($fd1)
                fclose($fd1);
            if ($fd2)
                fclose($fd2);
            throw new Exception("Failed to [$functionName] for src path [$srcFilePath] and dest [$dstFilePath] because " . $e->getMessage());
        }
    }

    public static function dumpEncryptFilePart($filePath,  $key, $iv, $rangeFrom, $rangeLength)
    {
        $tempPath = self::getClearTempPath($filePath);
        self::decryptFile($filePath, $key, $iv, $tempPath);
        infraRequestUtils::dumpFilePart($tempPath, $rangeFrom, $rangeLength);
        unlink($tempPath);
        return;
    }

    public static function fileSize($filePath, $key = null, $iv = null)
    {
        $size = kFileBase::fileSize($filePath);
        if (!$key)
            return $size;
        
        if ($size < self::ENCRYPT_INTERVAL)
            return strlen(self::getEncryptedFileContent($filePath, $key, $iv, 0, -1));

        $tempPath = self::getClearTempPath($filePath);
        self::decryptFile($filePath, $key, $iv, $tempPath);
        $size = kFileBase::fileSize($tempPath);
        unlink($tempPath);
        return $size;
    }
    
    public static function encryptFolder($dirName, $key, $iv)
    {
        $filesPaths = kFile::dirList($dirName);
        $done = true;
        foreach ($filesPaths as $filePath)
            $done &= self::encryptFile($filePath, $key, $iv);
        return $done;
    }

    public static function encrypt($path, $key, $iv)
    {
        if (is_file($path))
            return self::encryptFile($path, $key, $iv);
        else if (is_dir($path))
            return self::encryptFolder($path, $key, $iv);
    }
    
    public static function getClearTempPath($path)
    {
        return sys_get_temp_dir(). "/clear_" . pathinfo($path, PATHINFO_BASENAME);
    }

    
}