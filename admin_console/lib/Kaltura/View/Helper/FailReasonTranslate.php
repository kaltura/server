<?php
class Kaltura_View_Helper_FailReasonTranslate extends Zend_View_Helper_Abstract
{
	private static $curlHashes = array(
		0 => 'CURLEOK',
		1 => 'CURLEUNSUPPORTEDPROTOCOL',
		2 => 'CURLEFAILEDINIT',
		3 => 'CURLEURLMALFORMAT',
		5 => 'CURLECOULDNTRESOLVEPROXY',
		6 => 'CURLECOULDNTRESOLVEHOST',
		7 => 'CURLECOULDNTCONNECT',
		8 => 'CURLEFTPWEIRDSERVERREPLY',
		9 => 'CURLEREMOTEACCESSDENIED',
		11 => 'CURLEFTPWEIRDPASSREPLY',
		13 => 'CURLEFTPWEIRDPASVREPLY',
		14 => 'CURLEFTPWEIRD227FORMAT',
		84 => 'CURLEFTPPRETFAILED',
		15 => 'CURLEFTPCANTGETHOST',
		17 => 'CURLEFTPCOULDNTSETTYPE',
		18 => 'CURLEPARTIALFILE',
		19 => 'CURLEFTPCOULDNTRETRFILE',
		21 => 'CURLEQUOTEERROR',
		22 => 'CURLEHTTPRETURNEDERROR',
		23 => 'CURLEWRITEERROR',
		25 => 'CURLEUPLOADFAILED',
		26 => 'CURLEREADERROR',
		27 => 'CURLEOUTOFMEMORY',
		28 => 'CURLEOPERATIONTIMEDOUT',
		30 => 'CURLEFTPPORTFAILED',
		31 => 'CURLEFTPCOULDNTUSEREST',
		33 => 'CURLERANGEERROR',
		34 => 'CURLEHTTPPOSTERROR',
		35 => 'CURLESSLCONNECTERROR',
		36 => 'CURLEFTPBADDOWNLOADRESUME',
		37 => 'CURLEFILECOULDNTREADFILE',
		38 => 'CURLELDAPCANNOTBIND',
		39 => 'CURLELDAPSEARCHFAILED',
		41 => 'CURLEFUNCTIONNOTFOUND',
		42 => 'CURLEABORTEDBYCALLBACK',
		43 => 'CURLEBADFUNCTIONARGUMENT',
		45 => 'CURLEINTERFACEFAILED',
		47 => 'CURLETOOMANYREDIRECTS',
		48 => 'CURLEUNKNOWNTELNETOPTION',
		49 => 'CURLETELNETOPTIONSYNTAX',
		51 => 'CURLEPEERFAILEDVERIFICATION',
		52 => 'CURLEGOTNOTHING',
		53 => 'CURLESSLENGINENOTFOUND',
		54 => 'CURLESSLENGINESETFAILED',
		55 => 'CURLESENDERROR',
		56 => 'CURLERECVERROR',
		58 => 'CURLESSLCERTPROBLEM',
		59 => 'CURLESSLCIPHER',
		60 => 'CURLESSLCACERT',
		61 => 'CURLEBADCONTENTENCODING',
		62 => 'CURLELDAPINVALIDURL',
		63 => 'CURLEFILESIZEEXCEEDED',
		64 => 'CURLEUSESSLFAILED',
		65 => 'CURLESENDFAILREWIND',
		66 => 'CURLESSLENGINEINITFAILED',
		67 => 'CURLELOGINDENIED',
		68 => 'CURLETFTPNOTFOUND',
		69 => 'CURLETFTPPERM',
		70 => 'CURLEREMOTEDISKFULL',
		71 => 'CURLETFTPILLEGAL',
		72 => 'CURLETFTPUNKNOWNID',
		73 => 'CURLEREMOTEFILEEXISTS',
		74 => 'CURLETFTPNOSUCHUSER',
		75 => 'CURLECONVFAILED',
		76 => 'CURLECONVREQD',
		77 => 'CURLESSLCACERTBADFILE',
		78 => 'CURLEREMOTEFILENOTFOUND',
		79 => 'CURLESSH',
		80 => 'CURLESSLSHUTDOWNFAILED',
		81 => 'CURLEAGAIN',
		82 => 'CURLESSLCRLBADFILE',
		83 => 'CURLESSLISSUERERROR',
		
		//0 => 'CURLMOK',
		//1 => 'CURLMBADHANDLE',
		//2 => 'CURLMBADEASYHANDLE',
		//3 => 'CURLMOUTOFMEMORY',
		//4 => 'CURLMINTERNALERROR',
		//5 => 'CURLMBADSOCKET',
		//6 => 'CURLMUNKNOWNOPTION',
		//0 => 'CURLSHEOK',
		//1 => 'CURLSHEBADOPTION',
		//2 => 'CURLSHEINUSE',
		//3 => 'CURLSHEINVALID',
		//4 => 'CURLSHENOMEM',
	);
		
	private static $curlDescriptions = array(
		0 => 'All fine. Proceed as usual.',
		1 => 'The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn\'t use, it can be a misspelled protocol string or just a protocol libcurl has no code for.',
		2 => 'Very early initialization code failed. This is likely to be an internal error or problem.',
		3 => 'The URL was not properly formatted.',
		5 => 'Couldn\'t resolve proxy. The given proxy host could not be resolved.',
		6 => 'Couldn\'t resolve host. The given remote host was not resolved.',
		7 => 'Failed to connect() to host or proxy.',
		8 => 'After connecting to a FTP server, libcurl expects to get a certain reply back. This error code implies that it got a strange or bad reply. The given remote server is probably not an OK FTP server.',
		9 => 'We were denied access to the resource given in the URL.  For FTP, this occurs while trying to change to the remote directory.',
		11 => 'After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned.',
		13 => 'libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed.',
		14 => 'FTP servers return a 227-line as a response to a PASV command. If libcurl fails to parse that line, this return code is passed back.',
		84 => 'The FTP server does not understand the PRET command at all or does not support the given argument. Be careful when using ',
		15 => 'An internal failure to lookup the host used for the new connection.',
		17 => 'Received an error when trying to set the transfer mode to binary or ASCII.',
		18 => 'A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn\'t match the previously given size.',
		19 => 'This was either a weird reply to a \'RETR\' command or a zero byte transfer complete.',
		21 => 'When sending custom "QUOTE" commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.',
		22 => 'This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is &gt;= 400. (This error code was formerly known as CURLE_HTTP_NOT_FOUND.)',
		23 => 'An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.',
		25 => 'Failed starting the upload. For FTP, the server typically denied the STOR command. The error buffer usually contains the server\'s explanation for this. (This error code was formerly known as CURLE_FTP_COULDNT_STOR_FILE.)',
		26 => 'There was a problem reading a local file or an error returned by the read callback.',
		27 => 'A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.',
		28 => 'Operation timeout. The specified time-out period was reached according to the conditions.',
		30 => 'The FTP PORT command returned error. This mostly happens when you haven\'t specified a good enough address for libcurl to use. See ',
		31 => 'The FTP REST command returned error. This should never happen if the server is sane.',
		33 => 'The server does not support or accept range requests.',
		34 => 'This is an odd error that mainly occurs due to internal confusion.',
		35 => 'A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.',
		36 => 'Attempting FTP resume beyond file size.',
		37 => 'A file given with FILE:\/\/ couldn\'t be opened. Most likely because the file path doesn\'t identify an existing file. Did you check file permissions?',
		38 => 'LDAP cannot bind. LDAP bind operation failed.',
		39 => 'LDAP search failed.',
		41 => 'Function not found. A required zlib function was not found.',
		42 => 'Aborted by callback. A callback returned "abort" to libcurl.',
		43 => 'Internal error. A function was called with a bad parameter.',
		45 => 'Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections\' source IP address with CURLOPT_INTERFACE. (This error code was formerly known as CURLE_HTTP_PORT_FAILED.)',
		47 => 'Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.',
		48 => 'An option set with CURLOPT_TELNETOPTIONS was not recognized/known. Refer to the appropriate documentation.',
		49 => 'A telnet option string was Illegally formatted.',
		51 => 'The remote server\'s SSL certificate or SSH md5 fingerprint was deemed not OK.',
		52 => 'Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.',
		53 => 'The specified crypto engine wasn\'t found.',
		54 => 'Failed setting the selected SSL crypto engine as default!',
		55 => 'Failed sending network data.',
		56 => 'Failure with receiving network data.',
		58 => 'problem with the local client certificate.',
		59 => 'Couldn\'t use specified cipher.',
		60 => 'Peer certificate cannot be authenticated with known CA certificates.',
		61 => 'Unrecognized transfer encoding.',
		62 => 'Invalid LDAP URL.',
		63 => 'Maximum file size exceeded.',
		64 => 'Requested FTP SSL level failed.',
		65 => 'When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.',
		66 => 'Initiating the SSL Engine failed.',
		67 => 'The remote server denied curl to login (Added in 7.13.1)',
		68 => 'File not found on TFTP server.',
		69 => 'Permission problem on TFTP server.',
		70 => 'Out of disk space on the server.',
		71 => 'Illegal TFTP operation.',
		72 => 'Unknown TFTP transfer ID.',
		73 => 'File already exists and will not be overwritten.',
		74 => 'This error should never be returned by a properly functioning TFTP server.',
		75 => 'Character conversion failed.',
		76 => 'Caller must register conversion callbacks.',
		77 => 'Problem with reading the SSL CA cert (path? access rights?)',
		78 => 'The resource referenced in the URL does not exist.',
		79 => 'An unspecified error occurred during the SSH session.',
		80 => 'Failed to shut down the SSL connection.',
		81 => 'Socket is not ready for send/recv wait till it\'s ready and try again. This return code is only returned from ',
		82 => 'Failed to load CRL file (Added in 7.19.0)',
		83 => 'Issuer check failed (Added in 7.19.0)',
		-1 => 'This is not really an error. It means you should call ',
		
//		0 => 'Things are fine.',
//		1 => 'The passed-in handle is not a valid CURLM handle.',
//		2 => 'An easy handle was not good/valid. It could mean that it isn\'t an easy handle at all, or possibly that the handle already is in used by this or another multi handle.',
//		3 => 'You are doomed.',
//		4 => 'This can only be returned if libcurl bugs. Please report it to us!',
//		5 => 'The passed-in socket is not a valid one that libcurl already knows about. (Added in 7.15.4)',
//		6 => 'curl_multi_setopt() with unsupported option (Added in 7.15.4) ',
//		0 => 'All fine. Proceed as usual.',
//		1 => 'An invalid option was passed to the function.',
//		2 => 'The share object is currently in use.',
//		3 => 'An invalid share object was passed to the function.',
//		4 => 'Not enough memory was available. (Added in 7.12.0) ',
	);
			
	private static $httpDescriptions = array(
		100 => 'Continue', 
		101 => 'Switching Protocols', 
		102 => 'Processing (WebDAV) (RFC 2518)', 
		200 => 'OK', 
		201 => 'Created', 
		202 => 'Accepted', 
		203 => 'Non-Authoritative Information (since HTTP/1.1)', 
		204 => 'No Content', 
		205 => 'Reset Content', 
		206 => 'Partial Content', 
		207 => 'Multi-Status (WebDAV) (RFC 2518)', 
		300 => 'Multiple Choices', 
		301 => 'Moved Permanently', 
		302 => 'Found', 
		303 => 'See Other', 
		304 => 'Not Modified', 
		305 => 'Use Proxy (since HTTP/1.1)', 
		306 => 'Switch Proxy', 
		307 => 'Temporary Redirect (since HTTP/1.1)', 
		400 => 'Bad Reques', 
		401 => 'Unauthorize', 
		402 => 'Payment Require', 
		403 => 'Forbidden', 
		404 => 'Not Found', 
		405 => 'Method Not Allowe', 
		406 => 'Not Acceptabl', 
		407 => 'Proxy Authentication Required', 
		408 => 'Request Timeou', 
		409 => 'Conflic', 
		410 => 'Gon', 
		411 => 'Length Require', 
		412 => 'Precondition Faile', 
		413 => 'Request Entity Too Larg', 
		414 => 'Request-URI Too Lon', 
		415 => 'Unsupported Media Typ', 
		416 => 'Requested Range Not Satisfiabl', 
		417 => 'Expectation Faile', 
		418 => 'I\'m a teapo', 
		422 => 'Unprocessable Entity (WebDAV) (RFC 4918', 
		423 => 'Locked (WebDAV) (RFC 4918', 
		424 => 'Failed Dependency (WebDAV) (RFC 4918', 
		425 => 'Unordered Collection (RFC 3648', 
		426 => 'Upgrade Required (RFC 2817', 
		449 => 'Retry Wit', 
		450 => 'Blocked by Windows Parental Control', 
		500 => 'Internal Server Error', 
		501 => 'Not Implemented', 
		502 => 'Bad Gateway', 
		503 => 'Service Unavailable', 
		504 => 'Gateway Timeout', 
		505 => 'HTTP Version Not Supported', 
		506 => 'Variant Also Negotiates (RFC 2295)', 
		507 => 'Insufficient Storage (WebDAV) (RFC 4918)', 
		509 => 'Bandwidth Limit Exceeded (Apache bw/limited extension)', 
		510 => 'Not Extended (RFC 2774)',
	);
	
	private static $clientDescriptions = array(
		KalturaClientException::ERROR_UNSERIALIZE_FAILED => 'Failed to unserialize response',
		KalturaClientException::ERROR_FORMAT_NOT_SUPPORTED => 'unsupported format',
		KalturaClientException::ERROR_UPLOAD_NOT_SUPPORTED => 'upload is not supported',
		KalturaClientException::ERROR_CONNECTION_FAILED => 'HTTP connection failed',
		KalturaClientException::ERROR_READ_FAILED => 'Response read failed',
		KalturaClientException::ERROR_INVALID_PARTNER_ID => 'Invalid partner ID',
		KalturaClientException::ERROR_INVALID_OBJECT_TYPE => 'Invalid object type',
	);
	
	public function failReasonTranslate($status, $errType, $errNumber, $message)
	{
		if($status != KalturaBatchJobStatus::FAILED)
			return $this->view->enumTranslate('KalturaBatchJobStatus', $status);
			
		$errTypeDesc = $this->view->enumTranslate('KalturaBatchJobErrorTypes', $errType);
		$ret = $errTypeDesc;
		
		switch($errType)
		{
			case KalturaBatchJobErrorTypes::APP:
				$title = $this->view->enumTranslate('KalturaBatchJobAppErrors', $errNumber);
				$ret = "<span title=\"$title\">$errTypeDesc ($errNumber)</span>";
				break;
				
			case KalturaBatchJobErrorTypes::HTTP:
				$url = 'http://en.wikipedia.org/wiki/List_of_HTTP_status_codes';
				
				if($errNumber > 0)
					$url = 'http://en.wikipedia.org/wiki/HTTP_' . $errNumber;
						
				$title = $url;
				if(isset(self::$httpDescriptions[$errNumber]))
					$title = self::$httpDescriptions[$errNumber];
						
				$ret = "<a title=\"$title\" href=\"$url\" target=\"_blank\">$errTypeDesc ($errNumber)</a>";
				break;
				
			case KalturaBatchJobErrorTypes::CURL:
				$url = 'http://curl.haxx.se/libcurl/c/libcurl-errors.html';
				if(isset(self::$curlHashes[$errNumber]))
					$url .= '#' . self::$curlHashes[$errNumber];
					
				$title = $url;
				if(isset(self::$curlDescriptions[$errNumber]))
					$title = self::$curlDescriptions[$errNumber];
						
				$ret = "<a title=\"$title\" href=\"$url\" target=\"_blank\">$errTypeDesc ($errNumber)</a>";
				break;
				
			case KalturaBatchJobErrorTypes::RUNTIME:
				$ret = "<span title=\"$message\">$errTypeDesc ($errNumber)</span>";
				break;
				
			case KalturaBatchJobErrorTypes::Kaltura_API:
				$ret = "<span title=\"$message\">$errTypeDesc ($errNumber)</span>";
				break;
				
			case KalturaBatchJobErrorTypes::Kaltura_CLIENT:
				$title = $message;
				if(isset(self::$clientDescriptions[$errNumber]))
					$title = self::$clientDescriptions[$errNumber];
					
				$ret = "<span title=\"$title\">$errTypeDesc ($errNumber)</span>";
				break;
		}
		
		return $ret;
	}
	
	
	
 
}