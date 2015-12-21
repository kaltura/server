<?php
/**
 * Live media server object, represents a media server association with live stream entry 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveMediaServer
{
	/**
	 * @var int
	 */
	protected $mediaServerId;
	
	/**
	 * @var int
	 */
	protected $index;
	
	/**
	 * @var int
	 */
	protected $dc;
	
	/**
	 * @var string
	 */
	protected $hostname;
	
	/**
	 * @var int
	 */
	protected $time;
	
	/**
	 * @var string
	 */
	protected $applicationName;
	
	public function __construct($index, $hostname, $dc = null, $id = null, $applicationName = null)
	{
		$this->index = $index;
		$this->mediaServerId = $id;
		$this->hostname = $hostname;
		$this->dc = $dc;
		$this->time = time();
		$this->applicationName = $applicationName;
	}
	
	/**
	 * @return int $mediaServerId
	 */
	public function getMediaServerId()
	{
		return $this->mediaServerId;
	}
	
	/**
	 * @return MediaServerNode
	 */
	public function getMediaServer()
	{
		$mediaServer = ServerNodePeer::retrieveByPK($this->mediaServerId);

		if($mediaServer instanceof MediaServerNode)
			return $mediaServer;
		 
		return null;	
	}

	/**
	 * @return int $index
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * @return int $dc
	 */
	public function getDc()
	{
		return $this->dc;
	}

	/**
	 * @return string $hostname
	 */
	public function getHostname()
	{
		return $this->hostname;
	}

	/**
	 * @return int $time
	 */
	public function getTime()
	{
		return $this->time;
	}
	
	/**
	 * @return the $applicationName
	 */
	public function getApplicationName() {
		return $this->applicationName;
	}

}