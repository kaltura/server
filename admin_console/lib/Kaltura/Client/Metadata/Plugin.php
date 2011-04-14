<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Metadata_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_Metadata_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_Metadata_MetadataService
	 */
	public $metadata = null;

	/**
	 * @var Kaltura_Client_Metadata_MetadataProfileService
	 */
	public $metadataProfile = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->metadata = new Kaltura_Client_Metadata_MetadataService($client);
		$this->metadataProfile = new Kaltura_Client_Metadata_MetadataProfileService($client);
	}

	/**
	 * @return Kaltura_Client_Metadata_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_Metadata_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'metadata' => $this->metadata,
			'metadataProfile' => $this->metadataProfile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'metadata';
	}
}

