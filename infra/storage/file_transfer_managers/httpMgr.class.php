<?php
/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the FTP protocol.
 * For additional comments please look at the 'kFileTransferMgr' class.
 * 
 * @package infra
 * @subpackage Storage
 */
class httpMgr extends kFileTransferMgr
{
	/**
	 * @var string
	 */
	protected $userAgent = "\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6\"";
	
	/**
	 * @var resource
	 */
	protected $ch;
	
	/**
	 * @var string
	 */
	protected $server;
	
	/**
	 * @var string
	 */
	protected $fieldName = null;
	
	/**
	 * @var string
	 */
	protected $fileName = null;

	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		parent::__construct($options);
	
		if($options)
		{
			if(isset($options['userAgent']))
				$this->userAgent = $options['userAgent'];
				
			if(isset($options['fieldName']))
				$this->fieldName = $options['fieldName'];
				
			if(isset($options['fileName']))
				$this->fileName = $options['fileName'];
		}
	}

	
	/**
	 * @param string $url
	 * @return string
	 */
	public static function encodeUrl($url)
	{
		return str_replace(array(' ', '[', ']'), array('%20', '%5B', '%5D'), $url);
	}

	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/

	// ftp connect to server:port
	protected function doConnect($http_server, &$http_port)
	{
		// try connecting to server
		if (!$http_port || $http_port == 0)
			$http_port = 80;
			
		$http_server .= ':' . $http_port;
		try
		{
			$url_parts = parse_url($http_server);
			if(isset($url_parts["scheme"]))
			{
				if($url_parts["scheme"] != "http" && $url_parts["scheme"] != "https" )
				{
					KalturaLog::err("URL [$http_server] is not http");
					return false;
				}
			}
			else
			{
				$http_server = 'http://' . $http_server;
			}
		}
		catch ( Exception $exception )
		{
			$http_server = 'http://' . $http_server;
		}
			
		$this->server = $http_server;

		$this->ch = curl_init();

		curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		
		curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
		curl_setopt($this->ch, CURLOPT_FORBID_REUSE, true); 
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		
		return $this->ch;
	}


	// login to an existing connection with given user/pass
	protected function doLogin($http_user, $http_pass)
	{
		if(is_null($http_user) && is_null($http_pass))
			return true;
			
		curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->ch, CURLOPT_USERPWD, "{$http_user}:{$http_pass}");
		
		return true;
	}


	// login using a public key - not supported in FTP
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return false; // NOT SUPPORTED
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file,  $local_file)
	{		
		$url = $this->server . '/' . $remote_file;
		$url = self::encodeUrl($url);
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_RANGE, false);
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		
		$params = null;
		if($this->fieldName)
		{
			$params = array($this->fieldName => file_get_contents($local_file));
		}
		elseif($this->fileName)
		{
			$params = array($this->fileName => '@' . $local_file);
		}
		else
		{
			$params = file_get_contents($local_file);
		}
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
		
		$results = curl_exec($this->ch);
		if(!$results)
		{
			$errNumber = curl_errno($this->ch);
			$errDescription = curl_error($this->ch);
		
			if(!$results)
				throw new kFileTransferMgrException($errDescription, $errNumber);
		}
		
		return $results;
	}


	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{
		$url = $this->server . '/' . $remote_file;
		$url = self::encodeUrl($url);
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POST, false);
		curl_setopt($this->ch, CURLOPT_RANGE, false);
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		
		$results = curl_exec($this->ch);
		if(!$results)
		{
			$errNumber = curl_errno($this->ch);
			$errDescription = curl_error($this->ch);
			throw new kFileTransferMgrException($errDescription, $errNumber);
		}
		
		if($local_file)
			file_put_contents($local_file, $results);
			
		return $results;
	}


	// create a new directory on the server
	protected function doMkDir ($remote_path)
	{
		return false;
	}


	// chmod to the given remote file
	protected function doChmod ($remote_file, $chmod_code)
	{
		return false;
	}


	// check if the given file/dir exists on the server
	protected function doFileExists($remote_file)
	{
		$url = $this->server . '/' . $remote_file;
		$url = self::encodeUrl($url);
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POST, false);
		curl_setopt($this->ch, CURLOPT_RANGE, '0-0');
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		
		$results = curl_exec($this->ch);
		if(!$results)
		{
			curl_setopt($this->ch, CURLOPT_RANGE, false);
			curl_setopt($this->ch, CURLOPT_NOBODY, true);
			
			$results = curl_exec($this->ch);
		}

		if(!$results)
			return false;
			
		return true;
	}

	// return the current working directory
	protected function doPwd()
	{
		return '/';
	}

	// delete a file and return true/false according to success
	protected function doDelFile ($remote_file)
	{
		return false; // not supported
	}

	// delete a directory and return true/false according to success
	protected function doDelDir ($remote_path)
	{
		return false; // not supported
	}

	protected function doList ($remoteDir)
	{
		return false; // not supported
	}
	
	protected function doListFileObjects ($remoteDir)
	{
		return false; // not supported
	}
	
	protected function doFileSize($remote_file)
	{
	    return false; // not supported
	}
	
	protected function doModificationTime($remote_file)
	{
	    return false; // not supported
	}

	protected function shouldCheckExistingRemoteFile()
	{
		return false;
	}
	
	/*******************/
	/* Other functions */
	/*******************/

	// closes the FTP connection.
	public function __destruct( )
	{
		// close the connection
		if ($this->ch)
			curl_close($this->ch);
	}

}
