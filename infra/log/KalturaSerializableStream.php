<?php

/**
 * @package infra
 * @subpackage log
 */
class KalturaSerializableStream extends Zend_Log_Writer_Stream
{
	/**
	 * @var string
	 */
	protected $_url = null;

	/**
	 * @var string
	 */
	protected $_mode = null;
	
	/**
	 * @param $streamOrUrl string
	 * @param $mode string
	 */
	public function __construct($streamOrUrl, $mode = 'a')
	{
		if (is_resource($streamOrUrl))
			throw new Zend_Log_Exception("Cannot use KalturaSerializableStream with a resource");
		
		$this->_url = $streamOrUrl;
		$this->_mode = $mode;

		$this->initStream();
	}

   	public function __sleep()
	{
		return array("_filters", "_formatter", "_url", "_mode");
	}

   	public function __wakeup()
	{
		$this->initStream();
	}
	
	protected function initStream()
	{
		$errno = null;
		$errstr = null;
		if (strpos($this->_url, "://") !== false)
		{
			$this->_stream = @fsockopen($this->_url, 0, $errno, $errstr, 1);
		}
		else
		{
			$this->_stream = @fopen($this->_url, $this->_mode, false);
		}
		
		if (! $this->_stream)
		{
			throw new Zend_Log_Exception("\"{$this->_url}\" cannot be opened with mode \"{$this->_mode}\"");
		}
	}
}
