<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_ContentDistribution_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_ContentDistribution_DistributionProfileService
	 */
	public $distributionProfile = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_EntryDistributionService
	 */
	public $entryDistribution = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_DistributionProviderService
	 */
	public $distributionProvider = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_GenericDistributionProviderService
	 */
	public $genericDistributionProvider = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_GenericDistributionProviderActionService
	 */
	public $genericDistributionProviderAction = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->distributionProfile = new Kaltura_Client_ContentDistribution_DistributionProfileService($client);
		$this->entryDistribution = new Kaltura_Client_ContentDistribution_EntryDistributionService($client);
		$this->distributionProvider = new Kaltura_Client_ContentDistribution_DistributionProviderService($client);
		$this->genericDistributionProvider = new Kaltura_Client_ContentDistribution_GenericDistributionProviderService($client);
		$this->genericDistributionProviderAction = new Kaltura_Client_ContentDistribution_GenericDistributionProviderActionService($client);
	}

	/**
	 * @return Kaltura_Client_ContentDistribution_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_ContentDistribution_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'distributionProfile' => $this->distributionProfile,
			'entryDistribution' => $this->entryDistribution,
			'distributionProvider' => $this->distributionProvider,
			'genericDistributionProvider' => $this->genericDistributionProvider,
			'genericDistributionProviderAction' => $this->genericDistributionProviderAction,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'contentDistribution';
	}
}

