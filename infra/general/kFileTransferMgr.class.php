<?php


/*************************************************************************************
 * List of classes that extend 'kFileTransferMgr'.
 * Instances of these classes can be created using the 'getInstance($type)' function.
 *************************************************************************************/
class kFileTransferMgrType
{
	const FTP  = 1; // FTP Protocol
	const SCP  = 2; // SCP Protocol
	const SFTP = 3; // SFTP Protocol
}
// path where the classes extending kFileTransferMgr are stored relative to this file
define ("PATH_TO_MANAGERS", "file_transfer_managers");




/**********************************************************************************************************
 * List of exception types relevant to 'kFileTransferMgr'.
 * Should be used as the exception code (getCode()) when creating a 'kFileTransferMgrException' exception.
 **********************************************************************************************************/
class kFileTransferMgrException extends Exception
{
	const notYetConnected    = 1; // connection not yet established
	const cantConnect        = 2; // cannot connect to server/port
	const cantAuthenticate   = 3; // username or password problem
	const localFileNotExists = 4; // local file not found (when uploading a file)
	const remotePathNotValid = 5; // remote file / path string is not valid
	const remoteFileExists   = 6; // trying to putFile that already exists with $overwrite == false
	const otherError         = 99; // other - exception's getMessage() will provide more details
}



/*************************************************************
 * An abstract class that implements a file transfer manager.
 *************************************************************/
abstract class kFileTransferMgr
{
	/********************/
	/* Member Variables */
	/********************/

	// consts for function result values
	const FILETRANSFERMGR_RES_OK  = true;
	const FILETRANSFERMGR_RES_ERR = false;

	// resource used to identify the current connection
	protected $connection_id;
	// user's starting directory
	protected $start_dir;



	/*********************************************************************************************/
	/* Abstract functions that should be implemented in all classes extending 'kFileTransferMgr'.
	 /*********************************************************************************************/

	/**
	 * Should create a connection to the given server & port
	 *
	 * @param $ftp_server server hostname / ip address
	 * @param $ftp_port server port
	 *
	 * @return the connection resource identifier
	 */
	abstract protected function doConnect($server, &$port);

	/**
	 * Should login to a previous initiatied connection with the user / pass given.
	 *
	 * @param $user username
	 * @param $pass passwrod
	 * @param $ftp_passive_mode passive mode true/false - relevant for FTP only
	 *
	 * @return true / false according to success
	 */
	abstract protected function doLogin($user, $pass, $ftp_passive_mode = TRUE);

	/**
	 * Should login to a previous initiatied connection with the given public key
	 *
	 * @param $user remote username
	 * @param $pubKeyFile public key file
	 * @param $privKeyFile  private key file
	 * @param $passphrase if $privKeyFile is encrypted (which it should be), the passphrase must be provided
	 *
	 * @return true / false according to success
	 */
	abstract protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null);

	/**
	 * Should upload the given 'local_file' to the connected server with name 'remote_file'
	 *
	 * @param $remote_file remote file's name
	 * @param $local_file local file's name
	 * @param $ftp_mode ftp transfer mode (FTP_BINARY / FTP_ASCII) - relevant for FTP only
	 *
	 * @return true / false according to success
	 */
	abstract protected function doPutFile($remote_file, $local_file, $ftp_mode);

	/**
	 * Should download the fiven 'remote_file' from the server as 'local_file'
	 *
	 * @param $remote_file remote file's name
	 * @param $local_file local file's name
	 * @param $ftp_mode ftp transfer mode (FTP_BINARY / FTP_ASCII) - relevant for FTP only
	 *
	 * @return true / false according to success
	 */
	abstract protected function doGetFile ($remote_file, $local_file, $ftp_mode);

	/**
	 * Should create a new directory on the server.
	 *
	 * @param $remote_path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doMkDir ($remote_path);

	/**
	 * Should delete the given file on the server.
	 *
	 * @param string $remote_file remote file's name
	 *
	 * @return true / false according to success
	 */
	abstract protected function doDelFile ($remote_file);

	/**
	 * Should delete the given directory on the server (including all its contents)
	 *
	 * @param string $remote_path remote directory's name
	 *
	 * @return true / false according to success
	 */
	abstract protected function doDelDir ($remote_path);



	/**
	 * Should chmod the given file with the given code
	 *
	 * @param $remote_file
	 * @param $chmod_code
	 *
	 * @return true / false according to success
	 */
	abstract protected function doChmod ($remote_file, $chmod_code);

	/**
	 * Should return true/false if the given file or directory exists on the server
	 *
	 * @param $remote_file
	 *
	 * @return true / false according to success
	 */
	abstract protected function doFileExists($remote_file);

	/**
	 * Should return the current working directory as a string
	 *
	 * @return a string of the current working directory's path
	 */
	abstract protected function doPwd();


	/********************/
	/* Public Functions */
	/********************/

	/**
	 * Create a new class instance according to the given type.
	 *
	 * @param fileTransferMgrTypes $type Class type from the list under 'kFileTransferMgrType' class.
	 *
	 * @return kFileTransferMgr a new instance
	 */
	public static function getInstance($type)
	{
		switch($type)
		{
			case kFileTransferMgrType::FTP:
				return new ftpMgr();

			case kFileTransferMgrType::SCP:
				return new scpMgr();

			case kFileTransferMgrType::SFTP:
				return new sftpMgr();
		}

		return null;
	}


	/**
	 * Return the current connection identifier resource.
	 */
	public function getConnection ()
	{
		return $this->connection_id;
	}


	/**
	 * Connect & authenticate on the given server, using the given username & password.
	 *
	 * @param $server Server's hostname or IP address
	 * @param $user User's name
	 * @param $pass Password
	 * @param $port Server's listening port
	 * @param $ftp_passive_mode Used for FTP only
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function login ( $server, $user, $pass, $port = null, $ftp_passive_mode = true)
	{
		KalturaLog::debug("Login to server [$server] port [$port] username/password [$user/$pass] passive [$ftp_passive_mode]");
		
		$ftp_passive_mode = true;
		KalturaLog::debug("FTP passive mode changed to [true] because of a bug with ftp_put on fedora");
		
		$this->connection_id = @($this->doConnect($server, $port));
		if (!$this->connection_id) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException ("Can't connect [$server:$port] - " . $last_error['message'], kFileTransferMgrException::cantConnect);
		}
		
		if(@($this->doLogin($user, $pass, $ftp_passive_mode)))
		{
			KalturaLog::debug("Logged in successfully");
		}
		else
		{
			$last_error = error_get_last();
			throw new kFileTransferMgrException ( "Can't authenticate [$user] - " . $last_error['message'], kFileTransferMgrException::cantAuthenticate);
		}
		$this->start_dir = $this->doPwd();
	}


	/**
	 * Connect & authenticate on the given server, using the given given public key.
	 *
	 * @param $server Server's hostname or IP address
	 * @param $remoteUser remote username
	 * @param $pubKeyFile public key file
	 * @param $privKeyFile  private key file
	 * @param $port server's listening port
	 * @param $passphrase if $privKeyFile is encrypted (which it should be), the passphrase must be provided
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function loginPubKey ( $server, $user, $pubKeyFile, $privKeyFile, $passphrase = null, $port = null)
	{
		KalturaLog::debug("Login to server [$server] port [$port] username [$user] public key file [$pubKeyFile] private key file [$privKeyFile]");
		
		$this->connection_id = @($this->doConnect($server, $port));
		if (!$this->connection_id) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException ("Can't connect [$server:$port] - " . $last_error['message'], kFileTransferMgrException::cantConnect);
		}
		
		if(@($this->doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase)))
		{
			KalturaLog::debug("Logged in successfully");
		}
		else
		{
			$last_error = error_get_last();
			throw new kFileTransferMgrException ( "Can't authenticate [$user] - " . $last_error['message'], kFileTransferMgrException::cantAuthenticate);
		}
		$this->start_dir = $this->doPwd();
	}


	/**
	 * Upload a file to the server
	 *
	 * @param $remote_file Remote file name
	 * @param $local_file Local file name
	 * @param $overwrite true if should overwrite an existing remote file, or false otherwise
	 * @param $ftp_mode FTP_BINARY or FTP_ASCII - used for FTP only
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function putFile ($remote_file, $local_file, $overwrite = false, $ftp_mode = FTP_BINARY)
	{
		KalturaLog::debug("Puts file [$remote_file] from local [$local_file] overwrite [$overwrite]");
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}
		if (!file_exists($local_file)) {
			throw new kFileTransferMgrException("Can't find local file [$local_file]", kFileTransferMgrException::localFileNotExists);
		}
		if ($ftp_mode != FTP_ASCII)	{
			$ftp_mode = FTP_BINARY;
		}

		$remote_file = $this->fixPathString($remote_file);

		// delete existing file if overwrite == true
		$res = true;
		if ($overwrite) {
			if ($this->fileExists($remote_file)) {
				$res = @($this->delFile($remote_file));
			}
			// check if deletion was done succesfully
			if (!$res) {
				$last_error = error_get_last();
				throw new kFileTransferMgrException("Can't delete existing file [$remote_file] - " . $last_error['message'], kFileTransferMgrException::otherError);
				return self::FILETRANSFERMGR_RES_ERR;
			}
		}
		else { // $overwrite == false
			if ($this->fileExists($remote_file)) {
				throw new kFileTransferMgrException("Remote file [$remote_file] already exists.", kFileTransferMgrException::remoteFileExists);
			}
		}

		// create remote directory if necessary
		if (!$this->fileExists(dirname($remote_file))) {
			$this->mkDir(dirname($remote_file));
		}

		// try to upload file
		$res = @($this->doPutFile($remote_file, $local_file, $ftp_mode));

		// check response
		if ( !$res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't put file [$remote_file] - " . $last_error['message'], kFileTransferMgrException::otherError);
		}
		
		KalturaLog::debug("File uploaded successfully");
		return self::FILETRANSFERMGR_RES_OK;
	}
	
	/**
	 * Download a file from the server
	 *
	 * @param $remote_file Remote file name
	 * @param $local_file Local file name
	 * @param $ftp_mode FTP_BINARY or FTP_ASCII - used for FTP only
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function getFile ( $remote_file, $local_file, $ftp_mode = FTP_BINARY)
	{
		KalturaLog::debug("Gets file [$remote_file] to local [$local_file]");
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}
		if ($ftp_mode != FTP_ASCII)	{
			$ftp_mode = FTP_BINARY;
		}

		$remote_file = $this->fixPathString($remote_file);

		// try to download file
		$res = @($this->doGetFile($remote_file, $local_file, $ftp_mode));

		// check response
		if ( ! $res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't get file [$remote_file] - " . $last_error['message'], kFileTransferMgrException::otherError);
			return self::FILETRANSFERMGR_RES_ERR;
		}
		else
		{
			KalturaLog::debug("File retrieved successfully");
			return self::FILETRANSFERMGR_RES_OK;
		}
	}

	/**
	 * Create a new directory on the server
	 *
	 * @param $remote_path New directory path
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function mkDir ($remote_path)
	{
		KalturaLog::debug("Makes directory [$remote_path]");
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}
		if (strlen(trim($remote_path)) <= 0) {
			throw new kFileTransferMgrException("Remote path given is empty", kFileTransferMgrException::remotePathNotValid);
			return self::FILETRANSFERMGR_RES_ERR;
		}

		$remote_path = $this->fixPathString($remote_path);

		// recursivly try to create the new directory
		$temp_path = '';
		if ($remote_path[0] == '/' || $remote_path[0] == '\\') {
			$temp_path = '/';
		}
		$split_path = explode("/", $remote_path);
		$res = true;
		$i = 0;
		$array_count = count($split_path);
		while ($res && $i < $array_count) {
			if (($split_path[$i] != null) && (trim($split_path[$i]) != '') )
			{
				$temp_path = $temp_path . $split_path[$i];
				if (!$this->fileExists($temp_path)) { // direcotry doesn't exist
					$res = @($this->doMkDir($temp_path));
				}
				$temp_path = $temp_path . '/';
			}
			$i++;
		}

		// check response
		if ( !$res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't make directory [$remote_path] - " . $last_error['message'], kFileTransferMgrException::otherError);
		}
		
		KalturaLog::debug("Directory [$remote_path] created successfully");
		return self::FILETRANSFERMGR_RES_OK;
	}
	
	/**
	 * Chmod a remote file / directory
	 *
	 * @param $remote_file
	 * @param $chmod_code
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR
	 */
	public function chmod ($remote_file, $chmod_code)
	{
		KalturaLog::debug("Changes mode [$chmod_code] on file [$remote_file]");
		
		// parameter changes
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}

		$remote_file = $this->fixPathString($remote_file);

		// try to do chmod
		$res = @($this->doChmod($remote_file, $chmod_code));

		// check response
		if ( !$res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't change mode of [$remote_file] to [$chmod_code] - " . $last_error['message'], kFileTransferMgrException::otherError);
			return self::FILETRANSFERMGR_RES_ERR;
		}
		else
		{
			KalturaLog::debug("Mode changed successfully");
			return self::FILETRANSFERMGR_RES_OK;
		}
	}


	/**
	 * Checks if a remote file/dir exists
	 *
	 * @param $remote_file path to remote file or directory
	 *
	 * @throws kFileTransferMgrException
	 *
	 * @return FILETRANSFERMGR_RES_OK / FILETRANSFERMGR_RES_ERR	 *
	 */
	public function fileExists($remote_file)
	{
		KalturaLog::debug("Checking if file exists [$remote_file]");
		
		if ($this->start_dir && strpos($this->start_dir, $remote_file) === 0) {
			KalturaLog::debug("File is part of start directory - exists");
			return true;
		}
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}

		$remote_file = $this->fixPathString($remote_file);

		// check if file exists
		$res = @($this->doFileExists($remote_file));
		
		if($res)
		{
			KalturaLog::debug("File exists");
		}
		else
		{
			KalturaLog::debug("File does not exist");
		}
		
		return $res;
	}



	public function delFile ($remote_file)
	{
		KalturaLog::debug("Deleting file [$remote_file]");
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}

		$remote_file = $this->fixPathString($remote_file);

		// try to delete file
		$res = @($this->doDelFile($remote_file));

		// check response
		if ( !$res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't delete file [$remote_file] - " . $last_error['message'], kFileTransferMgrException::otherError);
			return self::FILETRANSFERMGR_RES_ERR;
		}
		else
		{
			KalturaLog::debug("File deleted successfully");
			return self::FILETRANSFERMGR_RES_OK;
		}
	}


	public function delDir ($remote_path)
	{
		KalturaLog::debug("Deleting directory [$remote_path]");
		
		// parameter checks
		if (!$this->connection_id) {
			throw new kFileTransferMgrException("No connection established yet.", kFileTransferMgrException::notYetConnected);
		}

		$remote_path = $this->fixPathString($remote_path);

		// try to delete directory
		$res = @($this->doDelDir($remote_path));

		// check response
		if ( !$res ) {
			$last_error = error_get_last();
			throw new kFileTransferMgrException("Can't delete directory [$remote_path] - " . $last_error['message'], kFileTransferMgrException::otherError);
			return self::FILETRANSFERMGR_RES_ERR;
		}
		else
		{
			KalturaLog::debug("Directory deleted successfully");
			return self::FILETRANSFERMGR_RES_OK;
		}
	}


	/***************************/
	/* Other private functions */
	/***************************/

	/**
	 * Empty PROTECTED constructor - should never be used.
	 * To get a new class instance use 'getInstance($type)'.
	 */
	protected function __construct()
	{
		// do nothing
	}

	/**
	 * changes \ to / in the given path string and removes the home directory path from the beginning
	 */
	private function fixPathString ($path)
	{
		KalturaLog::debug("Fix path [$path] with start directory [$this->start_dir]");
		
		$new_path = trim($path);
		$new_path = str_replace("\\", "/", $new_path);
		
		KalturaLog::debug("Fixed path [$new_path]");
		return $new_path;
	}

}

