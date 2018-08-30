<?php

/**
 * Created by IntelliJ IDEA.
 * User: David.Winder
 * Date: 10/22/2017
 * Time: 5:10 PM
 */
class kFileBase 
{
    /**
     * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
     * @param string $fileContent
     * @param string $prefix
     * @param string $postfix
     * @param integer $permission
     * @return string path to temporary file location
     */
    public static function createTempFile($fileContent, $prefix = '' , $permission = null, $postfix = null)
    {
        $tempDirectory = sys_get_temp_dir();
        if ($postfix )
            $tempDirectory .= ".$postfix";
        $fileLocation = tempnam($tempDirectory, $prefix);
        if (self::safeFilePutContents($fileLocation, $fileContent, $permission))
            return $fileLocation;
    }
    
    public static function safeFilePutContents($filePath, $var, $mode=null)
    {
        // write to a temp file and then rename, so that the write will be atomic
        $tempFilePath = tempnam(dirname($filePath), basename($filePath));
        if (kFileBase::kFilePutContents($tempFilePath, $var) === false)
            return false;
        if (kFileBase::kRename($tempFilePath, $filePath) === false)
        {
            @unlink($tempFilePath);
            return false;
        }
        if($mode)
        {
            self::chmod($filePath, $mode);
        }
        return true;
    }

    public static function chmod($filePath, $mode)
    {
        chmod($filePath, $mode);
    }

    public static function readLastBytesFromFile($file_name, $bytes = 1024)
    {
        $fh  = kFileBase::kFOpen($file_name,'r');
        $data = "";
        if($fh)
        {
            fseek($fh, - $bytes, SEEK_END);
            $data = fread($fh, $bytes);
        }

        fclose($fh);

        return $data;
    }

    static public function getFileNameNoExtension($file_name, $include_file_path = false)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $base_file_name = pathinfo($file_name, PATHINFO_BASENAME);
        $len = strlen($base_file_name) - strlen($ext);
        if(strlen($ext) > 0)
        {
            $len = $len - 1;
        }

        $res = substr($base_file_name, 0, $len);
        if($include_file_path)
        {
            $res = pathinfo($file_name, PATHINFO_DIRNAME) . "/" . $res;
        }
        return $res;
    }

    static public function replaceExt($file_name, $new_ext)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $len = strlen($ext);
        return ($len ? substr($file_name, 0, - strlen($ext)) : $file_name) . $new_ext;
    }

    // make sure the file is closed , then remove it
    public static function deleteFile($file_name)
    {
        $fh = kFileBase::kFOpen($file_name,'w') or die("can't open file");
        fclose($fh);
        unlink($file_name);
    }

    /**
     * creates a dirctory using the specified path
     * @param string $path
     * @param int $rights
     * @param bool $recursive
     * @return bool true on success or false on failure.
     */
    public static function fullMkfileDir ($path, $rights = 0777, $recursive = true)
    {
        if(file_exists($path))
            return true;

        $oldUmask = umask(00);
        $result = self::kMkDir($path, $rights, $recursive, null, true);
        umask($oldUmask);
        return $result;
    }
    
    /**
     *
     * creates a dirctory using the dirname of the specified path
     * @param string $path
     * @param int $rights
     * @param bool $recursive
     * @return bool true on success or false on failure.
     */
    public static function fullMkdir($path, $rights = 0755, $recursive = true)
    {
        return self::fullMkfileDir(dirname($path), $rights, $recursive);
    }
    
    /**
     * @param string $filename - path to file
     * @return float
     */
    static public function fileSize($filename)
    {
        if(PHP_INT_SIZE >= 8)
            return filesize($filename);

        $filename = str_replace('\\', '/', $filename);

        $url = "file://localhost/$filename";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $headers = curl_exec($ch);
        if(!$headers)
            KalturaLog::err('Curl error: ' . curl_error($ch));
        curl_close($ch);

        if(!$headers)
            return false;

        if (preg_match('/Content-Length: (\d+)/', $headers, $matches))
            return floatval($matches[1]);

        return false;
    }

    static public function appendToFile($file_name , $str)
    {
        kFileBase::kFilePutContents($file_name, $str, FILE_APPEND);
    }

    static public function fixPath($file_name)
    {
        $res = str_replace("\\", "/", $file_name);
        $res = str_replace("//", "/", $res);
        return $res;
    }
    
    static public function setFileContent($file_name, $content)
    {
        $res = null;
        $file_name = self::fixPath($file_name);

        // TODO - this code should be written in fullMkdir
        if(! file_exists(dirname($file_name)))
            self::fullMkdir($file_name);

        $fh  = kFileBase::kFOpen($file_name,'w');
        try
        {
            $res = fwrite($fh, $content);
        }
        catch(Exception $ex)
        {
            // whatever happens - don't forget to close $fh
        }
        fclose($fh);
        return $res;
    }
    
    static public function getFileContent($file_name, $from_byte = 0, $to_byte = -1, $mode = 'r')
    {
        $file_name = self::fixPath($file_name);

        try
        {
            if(! file_exists($file_name))
                return NULL;
            $fh  = kFileBase::kFOpen($file_name, $mode);

            if($fh == NULL)
                return NULL;
            if($from_byte > 0)
            {
                fseek($fh, $from_byte);
            }

            if($to_byte > 0)
            {
                $to_byte = min($to_byte, self::fileSize($file_name));
                $length = $to_byte - $from_byte;
            }
            else
            {
                $length = self::fileSize($file_name);
            }

            $theData = fread($fh, $length);
            fclose($fh);
            return $theData;
        }
        catch(Exception $ex)
        {
            return NULL;
        }
    }
    
    public static function mimeType($file_name)
    {
        if (!file_exists($file_name))
            return false;

        if(! function_exists('mime_content_type'))
        {
            $type = null;
            exec('file -i -b ' . realpath($file_name), $type);

            $parts = @ explode(";", $type[0]); // can be of format text/plain;  charset=us-ascii 


            return trim($parts[0]);
        }
        else
        {
            return mime_content_type($file_name);
        }
    }

    public static function copyFileMetadata($srcFile, $destFile)
    {
        @chown($destFile, fileowner($srcFile));
        @chgrp($destFile, filegroup($srcFile));
        $mode = substr(decoct(fileperms($srcFile)), -4);
        self::chmod($destFile,intval($mode,8));
    }

    public static function kFOpen($filePath, $mode, $suppressWarnings = false, $useIncludePath = null, $context = null)
    {
        $e = new Exception();
        KalturaLog::debug("\n****************************************** \n Opening file investigation $filePath :\n" . $e->getTraceAsString() . "\n****************************************** \n");
        if($context)
        {
            if ($suppressWarnings)
                return @fopen($filePath, $mode, $useIncludePath, $context);
            return fopen($filePath, $mode, $useIncludePath, $context);
        }

        if ($suppressWarnings)
            return @fopen($filePath, $mode, $useIncludePath);
        return fopen($filePath, $mode, $useIncludePath);
    }

    public static function kMkDir($filePath, $mode = 0777, $recursive = false, $context = null, $suppressWarnings = false)
    {
        $e = new Exception();
        KalturaLog::debug("\n****************************************** \n Making Dir investigation $filePath :\n" . $e->getTraceAsString() . "\n****************************************** \n");
        if ($context)
        {
            if ($suppressWarnings)
                return @mkdir($filePath, $mode, $recursive, $context);
            return mkdir($filePath, $mode, $recursive, $context);
        }

        if ($suppressWarnings)
            return @mkdir($filePath, $mode, $recursive);
        return mkdir($filePath, $mode, $recursive);
    }

    public static function kCopy($src, $dst, $context = null, $suppressWarnings = false)
    {
        $e = new Exception();
        KalturaLog::debug("Copying investigation from $src to $dst:\n" . $e->getTraceAsString() . "\n****************************************** \n");

        if ($context)
        {
            if ($suppressWarnings)
                return @copy($src, $dst, $context);
            return copy($src, $dst, $context);
        }

        if ($suppressWarnings)
            return @copy($src, $dst);
        return copy($src, $dst);
    }

    public static function kRename($src, $dst, $context = null, $suppressWarnings = false)
    {
        $e = new Exception();
        KalturaLog::debug("\n****************************************** \n Rename investigation from $src to $dst:\n" . $e->getTraceAsString() . "\n****************************************** \n");

        if ($context)
        {
            if ($suppressWarnings)
                return @rename($src, $dst, $context);
            return rename($src, $dst, $context);
        }

        if ($suppressWarnings)
            return @rename($src, $dst);
        return rename($src, $dst);
    }

    public static function kFilePutContents($filePath, $data, $flags = null, $context = null,  $suppressWarnings = false )
    {
        $e = new Exception();
        KalturaLog::debug("\n****************************************** \n file_put_contents investigation to $filePath:\n" . $e->getTraceAsString() . "\n****************************************** \n");
        if ($context)
        {
            if ($suppressWarnings)
                return @file_put_contents($filePath, $data, $flags, $context);
            return file_put_contents($filePath, $data, $flags, $context);
        }

        if ($suppressWarnings)
            return @file_put_contents($filePath, $data, $flags);
        return file_put_contents($filePath, $data, $flags);
    }


}