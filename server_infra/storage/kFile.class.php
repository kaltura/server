<?php
/**
 * @package infra
 * @subpackage Storage
 */
class kFile extends kFileBase
{
	public static function dumpFile($file_name, $mime_type = null, $max_age = null, $limit_file_size = 0)
	{
		self::closeDbConnections();
		
		$nfs_file_tries = 0;
		while(! file_exists($file_name))
		{
			//			clearstatcache(true,$file_name);
			clearstatcache();
			$nfs_file_tries ++;
			if($nfs_file_tries > 3) // if after 9 seconds file did not appear in NFS - probably not found...
			{
				break;
			
		// when breaking, kFile will try to dump, if file not exist - will die...
			}
			else
			{
				sleep(3);
			}
		}
		
		// if by now there is no file - die !
		if(! file_exists($file_name))
			die();
		
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$total_length = $limit_file_size ? $limit_file_size : self::fileSize($file_name);
		
		$useXSendFile = false;
		if (in_array('mod_xsendfile', apache_get_modules()))
		{
			$xsendfile_uri = kConf::hasParam('xsendfile_uri') ? kConf::get('xsendfile_uri') : null;
			if ($xsendfile_uri !== null && strpos($_SERVER["REQUEST_URI"], $xsendfile_uri) !== false)
			{
				$xsendfile_paths = kConf::hasParam('xsendfile_paths') ? kConf::get('xsendfile_paths') : array();
				foreach($xsendfile_paths as $path)
				{
					if (strpos($file_name, $path) === 0)
					{
						header('X-Kaltura-Sendfile:');
						$useXSendFile = true;
						break;
					}
				}
			}
		}

		if ($useXSendFile)
			$range_length = null;
		else
		{
			// get range parameters from HTTP range requst headers
			list($range_from, $range_to, $range_length) = infraRequestUtils::handleRangeRequest($total_length);
		}
		
		if($mime_type)
		{
			infraRequestUtils::sendCdnHeaders($file_name, $range_length, $max_age, $mime_type);
		}
		else
			infraRequestUtils::sendCdnHeaders($ext, $range_length, $max_age);

		// return "Accept-Ranges: bytes" header. Firefox looks for it when playing ogg video files
		// upon detecting this header it cancels its original request and starts sending byte range requests
		header("Accept-Ranges: bytes");
		header("Access-Control-Allow-Origin:*");		

		if ($useXSendFile)
		{
			if (isset($GLOBALS["start"]))
				header("X-Kaltura:dumpFile:".(microtime(true) - $GLOBALS["start"]));
			header("X-Sendfile: $file_name");
			die;
		}

		$chunk_size = 100000;
		$fh = fopen($file_name, "rb");
		if($fh)
		{
			$pos = 0;
			fseek($fh, $range_from);
			while($range_length > 0)
			{
				$content = fread($fh, min($chunk_size, $range_length));
				echo $content;
				$range_length -= $chunk_size;
			}
			fclose($fh);
		}
		
		die();
	}
}
