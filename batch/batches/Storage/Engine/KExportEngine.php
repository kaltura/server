<?php
/**
 * 
 */
abstract class KExportEngine
{
	/**
	 * @var KalturaStorageJobData
	 */
	protected $data;
	
	/**
	 * @param KalturaStorageJobData $data
	 */
	public function __construct(KalturaStorageJobData $data)
	{
		$this->data = $data;
	}
	
	/**
	 * @return bool
	 */
	abstract function export ();
	
	
	/**
	 * @return bool
	 */
	abstract function verifyExportedResource ();
    
    /**
     * @return bool
     */
    abstract function delete();
	
	/**
	 * @param int $protocol
	 * @param KalturaStorageExportJobData $data
	 * @return KExportEngine
	 */
	public static function getInstance ($protocol, $partnerId, KalturaStorageJobData $data)
	{
		switch ($protocol)
		{
			case KalturaStorageProfileProtocol::FTP:
			case KalturaStorageProfileProtocol::KALTURA_DC:
			case KalturaStorageProfileProtocol::S3:
			case KalturaStorageProfileProtocol::SCP:
			case KalturaStorageProfileProtocol::SFTP:
			case KalturaStorageProfileProtocol::LOCAL:
				return new KFileTransferExportEngine($data, $protocol);
			default:
				return KalturaPluginManager::loadObject('KExportEngine', $protocol, array($data, $partnerId));
		}
	}
}