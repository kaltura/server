<?php
class KalturaGenericDistributionProviderAction extends KalturaObject implements IFilterable
{
	/**
	 * Auto generated
	 * 
	 * @readonly
	 * @var int
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Generic distribution provider action creation date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Generic distribution provider action last update date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var int
	 * @insertonly
	 * @filter eq,in
	 */
	public $genericDistributionProviderId;

	/**
	 * @var KalturaDistributionAction
	 * @insertonly
	 * @filter eq,in
	 */
	public $action;

	/**
	 * @var KalturaGenericDistributionProviderStatus
	 * @readonly
	 */
	public $status;

	/**
	 * @var KalturaGenericDistributionProviderParser
	 */
	public $resultsParser;

	/**
	 * @var KalturaDistributionProtocol
	 */
	public $protocol;

	/**
	 * @var string
	 */
	public $serverAddress;

	/**
	 * @var string
	 */
	public $remotePath;

	/**
	 * @var string
	 */
	public $remoteUsername;

	/**
	 * @var string
	 */
	public $remotePassword;

	/**
	 * @var string
	 */
	public $editableFields;

	/**
	 * @var string
	 */
	public $mandatoryFields;

	/**
	 * @readonly
	 * @var string
	 */
	public $mrssTransformer;

	/**
	 * @readonly
	 * @var string
	 */
	public $mrssValidator;

	/**
	 * @readonly
	 * @var string
	 */
	public $resultsTransformer;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'id',
		'createdAt',
		'updatedAt',
		'genericDistributionProviderId',
		'action',
		'status',
		'resultsParser',
		'protocol',
		'serverAddress',
		'remotePath',
		'remoteUsername',
		'remotePassword',
		'editableFields',
		'mandatoryFields',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);

		$key = $source_object->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER);
		$this->mrssTransformer = kFileSyncUtils::file_get_contents($key, true, false);

		$key = $source_object->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR);
		$this->mrssValidator = kFileSyncUtils::file_get_contents($key, true, false);

		$key = $source_object->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER);
		$this->resultsTransformer = kFileSyncUtils::file_get_contents($key, true, false);
	}
	
	public function getExtraFilters()
	{
		return array(
		);
	}
	
	public function getFilterDocs()
	{
		return array(
		);
	}
}