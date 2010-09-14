<?php
require_once("bootstrap.php");

/**
 * Will encapsultate the
 *
 * @package Scheduler
 * @subpackage DWH
 */
class KDwhClient
{
	private static $s_file_name;

	/* Holds the PHP stream to log to.
	 * @var null|stream
	 */
	protected $_stream = null;

	/**
	 * @var KDwhClient
	 */
	private static $s_dwh_client = null;


	/**
	 * @param string $file_name
	 */
	public static function setFileName ( $file_name )
	{
		self::$s_file_name = $file_name;
	}

	/**
	 * @param KDwhEventBase $event
	 */
	public static function send ( KDwhEventBase $event )
	{
		$dwh_client = self::getInstance();

		$event_line = $event->toEventLine();
		
		$dwh_client->write ( $event_line );
	}

	public static function getInstance ( )
	{
		if(is_null(self::$s_dwh_client))
			self::$s_dwh_client = new KDwhClient( self::$s_file_name );

		return self::$s_dwh_client;
	}


	/**
	 * Class Constructor
	 *
	 * @param  streamOrUrl     Stream or URL to open as a stream
	 * @param  mode            Mode, only applicable if a URL is given
	 */
	public function __construct($streamOrUrl, $mode = 'a')
	{
		if (is_resource($streamOrUrl)) {
			if (get_resource_type($streamOrUrl) != 'stream') {
				throw new KDwhClientException('Resource is not a stream');
			}

			if ($mode != 'a') {
				throw new KDwhClientException('Mode cannot be changed on existing streams');
			}

			$this->_stream = $streamOrUrl;
		} else {
			if (! $this->_stream = @fopen($streamOrUrl, $mode, false)) {
				$msg = "\"$streamOrUrl\" cannot be opened with mode \"$mode\"";
				throw new KDwhClientException($msg);
			}
		}
	}

	/* Close the stream resource.
	 *
	 * @return void
	 */
	public function shutdown()
	{
		if (is_resource($this->_stream))
		{
			fclose($this->_stream);
		}
	}

	public function write ( $event_line )
	{
		if (false === @fwrite($this->_stream, $event_line)) 
		{
			throw new KDwhClientException("Unable to write to stream");
		}
	}
	
	public function __destruct()
	{
		$this->shutdown();
	}
}
?>