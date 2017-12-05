<?php
/**
 * @package infra
 * @subpackage Storage
 */
class kFile extends kFileBase
{
	const MO_PATTERN = "GNU message catalog";
	const TEXT = "text";

	/**
	 * Returns directory $path contents as an array of :
	 *  array[0] = name
	 *  array[1] = type (dir/file)
	 *  array[2] = filesize
	 * @param string $path
	 * @param string $pathPrefix
	 */
	public static function listDir($path, $pathPrefix = '')
	{
		$fileList = array();
		$path = str_ireplace(DIRECTORY_SEPARATOR, '/', $path);
		$handle = opendir($path);
		if ($handle)
		{
		    while (false !== ($file = readdir($handle)))
		    {
		    	if ($file != '.' && $file != '..')
		    	{
		    		$fullPath = $path.'/'.$file;
		    		$tmpPrefix = $pathPrefix.$file;
		    		
			    	if (is_dir($fullPath))
			    	{
			    		$tmpPrefix = $tmpPrefix.'/';
			    		$fileList[] = array($tmpPrefix, 'dir', self::fileSize($fullPath));
			    		$fileList = array_merge($fileList, self::listDir($fullPath, $tmpPrefix));
			    	}	
			    	else
			    	{
			    		$fileList[] = array($tmpPrefix, 'file', self::fileSize($fullPath));
			    	}	    	
		    	}
		    }
		    closedir($handle);
		}
		return $fileList;
	}

	// TODO - implement recursion
	static public function dirList($directory, $return_directory_as_prefix = true, $should_recurse = false)
	{
		// create an array to hold directory list
		$results = array();
		
		// create a handler for the directory
		$handler = @opendir($directory);
		if(! $handler)
			KalturaLog::info("dirList $directory does not exist");
		
		// keep going until all files in directory have been read
		while($file = readdir($handler))
		{
			
			// if $file isn't this directory or its parent,
			// add it to the results array
			if($file != '.' && $file != '..')
			{
				$results[] = ($return_directory_as_prefix ? $directory . "/" : "") . $file;
			}
		}
		
		// tidy up: close the handler
		closedir($handler);
		
		// done!
		return $results;
	}
	
	/*
	 * Besure to limit the search with $max_results if not all files are reqquired
	 */
	static public function recursiveDirList($directory, $return_directory_as_prefix = true, $should_recurse = false, $file_pattern = NULL, $depth = 0, $max_results = -1)
	{
		if($depth > 10)
		{
			// exceeded the recursion depth
			return NULL;
		}
		
		$depth ++;
		
		// create an array to hold directory list
		$results = array();
		// create a handler for the directory
		$handler = @opendir($directory);
		if(! $handler)
			return NULL;
		
		//		echo  ( "directory: " .$directory . "<br>" );
		

		$current_path = pathinfo($directory, PATHINFO_DIRNAME) . "/" . pathinfo($directory, PATHINFO_BASENAME) . "/";
		// keep going until all files in directory have been read
		while(($file = readdir($handler)) != NULL)
		{
			// if $file isn't this directory or its parent,
			// add it to the results array
			if($file != '.' && $file != '..')
			{
				$match = 1;
				if($file_pattern != NULL)
				{
					$match = preg_match($file_pattern, $file);
				}
				
				if($match > 0)
				{
					$results[] = ($return_directory_as_prefix ? $directory . "/" : "") . $file;
					if($max_results > 1 && count($results) > $max_results)
						return $results;
				}
				
				if($should_recurse && is_dir($current_path . $file))
				{
					//				echo "Recursing... [$should_recurse] [$current_path $file]<br>"; 	
					

					$child_dir_results = self::recursiveDirList($current_path . $file, $return_directory_as_prefix, $should_recurse, $file_pattern, $depth);
					if($child_dir_results)
					{
						$results = kArray::append($results, $child_dir_results);
					}
				}
			}
		}
		// tidy up: close the handler
		closedir($handler);
		
		// done!
		return $results;
	}
	
	/**
	 * the result is a list of tuples - files_name , files_size 
	 */
	// TODO - implement recursion
	static public function dirListExtended($directory, $return_directory_as_prefix = true, $should_recurse = false, $file_pattern = NULL, $depth = 0, $fetch_content = false)
	{
		if($depth > 10)
		{
			// exceeded the recursion depth
			return NULL;
		}
		
		// create an array to hold directory list
		$results = array();
		
		// create a handler for the directory
		$handler = @opendir($directory);
		if(! $handler)
			return NULL;
		
		//		echo  ( "directory: " .$directory . "<br>" );
		

		$current_path = pathinfo($directory, PATHINFO_DIRNAME) . "/" . pathinfo($directory, PATHINFO_BASENAME) . "/";
		// keep going until all files in directory have been read
		while(($file = readdir($handler)) != NULL)
		{
			
			// if $file isn't this directory or its parent,
			// add it to the results array
			if($file != '.' && $file != '..')
			{
				if(! $file_pattern)
					$match = 1;
				else
					$match = preg_match($file_pattern, $file);
				
				if($match > 0)
				{
					$file_full_path = $directory . "/" . $file;
					$result = array();
					// first - name (with or without the full path)
					$result[] = ($return_directory_as_prefix ? $directory . "/" : "") . $file;
					// second - size 
					$result[] = self::fileSize($file_full_path);
					// third - time
					$result[] = filemtime($file_full_path);
					// forth - content (only if requested
					if($fetch_content)
						$result[] = file_get_contents($file_full_path);
					$results[] = $result;
				}
				
				if($should_recurse && is_dir($current_path . $file))
				{
					//				echo "Recursing... [$should_recurse] [$current_path $file]<br>"; 	
					

					$child_dir_results = self::dirListExtended($current_path . $file, $return_directory_as_prefix, $should_recurse, $file_pattern, ++ $depth);
					if($child_dir_results)
					{
						$results = kArray::append($results, $child_dir_results);
					}
				}
			}
		}
		
		// tidy up: close the handler
		closedir($handler);
		
		// done!
		return $results;
	}
	
	/**
	 * copies src to destination.
	 * Doesn't support non-flat directories!
	 * One can't use rename because rename isn't supported between partitions.
	 */
	private static function copyRecursively($src, $dest, $deleteSrc = false) {
		if (is_dir($src)) {
			
			// Generate target directory
			if (file_exists ($dest)) {
				if (! is_dir($dest)) {
					KalturaLog::err("Can't override a file with a directory [$dest]");
					return false;
				}
			} else {
				if (! mkdir($dest)) {
					KalturaLog::err("Failed to create directory [$dest]");
					return false;
				}
			}
			
			// Copy files
			$dir = dir($src);
			while ( false !== $entry = $dir->read () ) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				
				$newSrc = $src . DIRECTORY_SEPARATOR . $entry;
				if(is_dir($newSrc)) {
					KalturaLog::err("Copying of non-flat directroeis is illegal");
					return false;
				}
				
				$res =  self::copySingleFile ($newSrc, $dest . DIRECTORY_SEPARATOR . $entry , $deleteSrc);
				if (! $res)
					return false;
			}
			
			// Delete source
			if ($deleteSrc && (! rmdir($src))) {
				KalturaLog::err("Failed to delete source directory : [$src]");
				return false;
			}
		} else {
			self::copySingleFile($src, $dest, $deleteSrc);
		}
		return true;
	}
	
	private static function copySingleFile($src, $dest, $deleteSrc) {
		if($deleteSrc) {
			// In case of move, first try to move the file before copy & unlink.
			$startTime = microtime(true);
			if(rename($src, $dest))
			{
				KalturaLog::log("rename took : ".(microtime(true) - $startTime)." [$src] to [$dest] size: ".filesize($dest));
				return true;
			}
			
			KalturaLog::err("Failed to rename file : [$src] to [$dest]");
		}
		
		if (!copy($src,$dest)) {
			KalturaLog::err("Failed to copy file : [$src] to [$dest]");
			return false;
		}
		if ($deleteSrc && (!unlink($src))) {
			KalturaLog::err("Failed to delete source file : [$src]");
			return false;
		}
		return true;
	}
	
	public static function moveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		$from = str_replace("\\", "/", $from);
		$to = str_replace("\\", "/", $to);
		
		// Validation
		if(!file_exists($from))
		{
			KalturaLog::err("Source doesn't exist [$from]");
			return false;
		}
		
		if(strpos($to,'\"') !== false)
		{
			KalturaLog::err("Illegal destination file [$to]");
			return false;
		}
		
		// Preperation
		if($override_if_exists && is_file($to))
		{
			self::deleteFile($to);
		}
		
		if(! is_dir(dirname($to)))
		{
			self::fullMkdir($to);
		}
		
		// Copy
		return self::copyRecursively($from,$to, !$copy);
	}
	
	public static function linkFile($from, $to, $overrideIfExists = false, $copyIfLinkFailed = true)
	{
		$from = str_replace("\\", "/", $from);
		$to = str_replace("\\", "/", $to);
		
		if($overrideIfExists && (is_file($to) || is_link($to)))
		{
			self::deleteFile($to);
		}
		
		if(! is_dir(dirname($to)))
		{
			self::fullMkdir($to);
		}
		
		if(!file_exists($from))
		{
			KalturaLog::err("Source file doesn't exist [$from]");
			return false;
		}
		
		if(strpos($to,'\"') !== false)
		{
			KalturaLog::err("Illegal destination file [$to]");
			return false;
		}
			
		if(symlink($from, $to)) 
			return true;
		
		$out_arr = array();
		$rv = 0;
		exec("ln -s \"$from\" \"$to\"", $out_arr, $rv);
//			echo "RV($rv)\n";
		if($rv==0)
			return true;
			
		if(!$copyIfLinkFailed)
			return false;
			
		return self::moveFile($from, $to, $overrideIfExists, true);
	}
	
	//
	// downloadHeader - 1 only body, 2 - only header, 3 - both body and header
	//
	static public function downloadUrlToString($sourceUrl, $downloadHeader = 1, $extraHeaders = null)
	{
		// create a new curl resource
		// TODO - remove this hack !!!
		// I added it only because for some reason or other I couldn't get hold of the http_get 
		/*
		if ( function_exists ('http_get'))
		{
			return http_get($sourceUrl, array('redirect' => 5));
			
		}
		else
		*/
		{
			$ch = curl_init();
			
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $sourceUrl);
			curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
			curl_setopt($ch, CURLOPT_HEADER, ($downloadHeader & 2) ? 1 : 0);
			curl_setopt($ch, CURLOPT_NOBODY, ($downloadHeader & 1) ? 0 : 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			if($extraHeaders)
				curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);
			
		// grab URL and pass it to the browser
			$content = curl_exec($ch);
			
			// close curl resource, and free up system resources
			curl_close($ch);
		
		}
		return $content;
	}
	
	public static function getFileData($file_full_path)
	{
		return new kFileData($file_full_path);
	}
	
	public static function getFileDataWithContent($file_full_path)
	{
		$add_content = (strpos($file_full_path, ".txt") !== false || strpos($file_full_path, ".log") !== false);
		
		return new kFileData($file_full_path, $add_content);
	
	}
	

	public static function read_header($ch, $string)
	{
		$length = strlen($string);

		// we shouldnt return a chunked encoded header as we read the whole response and echo it after curl extracts it
		if (stripos($string, "Transfer-Encoding: chunked") === FALSE)
		{
			header($string);
		}

		return $length;
	}
	
	public static function read_body($ch, $string)
	{
		$length = strlen($string);
		echo $string;
		return $length;
	}
	
	public static function getRequestHeaders()
	{
		if(function_exists('apache_request_headers'))
			return apache_request_headers();
		
		foreach($_SERVER as $key => $value)
		{
			if(substr($key, 0, 5) == "HTTP_")
			{
				$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
				$out[$key] = $value;
			}
		}
		return $out;
	}

	public static function cacheRedirect($url)
	{
		if (function_exists('apc_store'))
		{
			$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
			apc_store("redirect-".$protocol.$_SERVER["REQUEST_URI"], $url, 60);
		}
	}
	
	public static function closeDbConnections()
	{
		// close all opened db connetion while we end an action with a long executing operation such as dumping a file.
		// this will limit the number of concurrent db connections as dumping a file make take a long time

		try
		{
			Propel::close();
		}
		catch(Exception $e)
		{
			KalturaLog::err("closeDbConnections: error closing db $e");
		}
	}

	/**
	 * Check if the file is executable
	 * @param string $path
	 *
	 * @return string
	 */
	public static function getMediaInfoFormat($path)
	{
		$mediaInfoParser = new KMediaInfoMediaParser($path);
		$mediaInfo = $mediaInfoParser->getMediaInfo();
		return $mediaInfo->containerFormat;
	}

	/**
	 * Try to find the file type by running the file cmd and match the output to a pattern
	 * It will return empty string if no pattern was matched
	 */
	public static function findFileTypeByFileCmd($filePath)
	{
		$fileType = '';
		$realPath = realpath($filePath);
		if($realPath)
		{
			$fileBrief = shell_exec('file -b ' . $realPath);
			if(kString::beginsWith($fileBrief,self::MO_PATTERN))
				$fileType = 'application/mo';
		}
		return $fileType;
	}

	public static function getFileDescription($realPath)
	{
		return shell_exec('file -b '.$realPath);
	}

	
}

/**
 * @package infra
 * @subpackage Storage
 */
class kFileData
{
	public $exists;
	public $full_path = NULL;
	public $name = NULL;
	public $size = NULL;
	public $timestamp = NULL;
	public $ext = NULL;
	public $content = NULL;
	public $raw_timestamp = NULL;
	
	public function __construct($full_file_path, $add_content = false)
	{
		//debugUtils::st();
		$this->full_path = realpath($full_file_path);
		$this->exists = file_exists($full_file_path);
		$this->name = pathinfo($full_file_path, PATHINFO_BASENAME);
		$this->ext = pathinfo($full_file_path, PATHINFO_EXTENSION);
		
		if($this->exists)
		{
			$this->size = self::fileSize($full_file_path);
			$this->raw_timestamp = filectime($full_file_path);
			$this->timestamp = date("Y-m-d H:i:s.", $this->raw_timestamp);
			
			if($add_content)
			{
				$this->content = file_get_contents($full_file_path);
			}
		}
	}
}
