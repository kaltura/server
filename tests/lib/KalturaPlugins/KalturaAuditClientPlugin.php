<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");


class KalturaAuditTrailService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("audit_audittrail", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrailListResponse");
		return $resultObject;
	}

	function add(KalturaAuditTrail $auditTrail)
	{
		$kparams = array();
		$this->client->addParam($kparams, "auditTrail", $auditTrail->toParams());
		$this->client->queueServiceActionCall("audit_audittrail", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrail");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("audit_audittrail", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrail");
		return $resultObject;
	}
}
class KalturaAuditClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAuditClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaAuditTrailService
	 */
	public $auditTrail = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->auditTrail = new KalturaAuditTrailService($client);
	}

	/**
	 * @return KalturaAuditClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAuditClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'auditTrail' => $this->auditTrail,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'audit';
	}
}

