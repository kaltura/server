<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");


class KalturaAnnotationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaAnnotationFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotationListResponse");
		return $resultObject;
	}

	function add(KalturaAnnotation $annotation)
	{
		$kparams = array();
		$this->client->addParam($kparams, "annotation", $annotation->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotation");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("annotation_annotation", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotation");
		return $resultObject;
	}

	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("annotation_annotation", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function update($id, KalturaAnnotation $annotation)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "annotation", $annotation->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotation");
		return $resultObject;
	}
}
class KalturaAnnotationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAnnotationClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaAnnotationService
	 */
	public $annotation = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->annotation = new KalturaAnnotationService($client);
	}

	/**
	 * @return KalturaAnnotationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAnnotationClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'annotation' => $this->annotation,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'annotation';
	}
}

