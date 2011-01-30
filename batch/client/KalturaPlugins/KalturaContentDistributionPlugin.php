<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaContentDistributionPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDistributionProfileService
	 */
	public $distributionProfile = null;

	/**
	 * @var KalturaEntryDistributionService
	 */
	public $entryDistribution = null;

	/**
	 * @var KalturaDistributionProviderService
	 */
	public $distributionProvider = null;

	/**
	 * @var KalturaGenericDistributionProviderService
	 */
	public $genericDistributionProvider = null;

	/**
	 * @var KalturaGenericDistributionProviderActionService
	 */
	public $genericDistributionProviderAction = null;

	/**
	 * @var KalturaContentDistributionBatchService
	 */
	public $contentDistributionBatch = null;

	protected function __construct()
	{
		parent::__construct();
		$this->distributionProfile = new KalturaDistributionProfileService();
		$this->entryDistribution = new KalturaEntryDistributionService();
		$this->distributionProvider = new KalturaDistributionProviderService();
		$this->genericDistributionProvider = new KalturaGenericDistributionProviderService();
		$this->genericDistributionProviderAction = new KalturaGenericDistributionProviderActionService();
		$this->contentDistributionBatch = new KalturaContentDistributionBatchService();
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new KalturaContentDistributionPlugin();
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			$this->distributionProfile,
			$this->entryDistribution,
			$this->distributionProvider,
			$this->genericDistributionProvider,
			$this->genericDistributionProviderAction,
			$this->contentDistributionBatch,
		);
		return $services;
	}
}

