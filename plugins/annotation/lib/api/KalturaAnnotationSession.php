<?php
class KalturaAnnotationSession extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaAnnotationSessionStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $entryId;

	/**
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $partnerId;


	/**
	 * @var string
	 * @filter eq,in
	 */
	public $userId;

	/**
	 * @var string
	 * @readonly
	 */
	public $data;
	
	private static $map_between_objects = array
	(
		"id",
		"createdAt",
		"updatedAt",
		"status",
		"entryId",
		"partnerId",
		"userId" => "puserId", //TODO - what will be the name of user?
		"data",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/**
	 * @param AnnotationSession $dbAnnotationSession
	 * @param array $propsToSkip
	 * @return AnnotationSession
	 */
	public function toObject($dbAnnotationSession = null, $propsToSkip = array())
	{
		if(is_null($dbAnnotationSession))
			$dbAnnotationSession = new AnnotationSession();
			
		return parent::toObject($dbAnnotationSession, $propsToSkip);
	}

	/**
	 * @param AnnotationSession $dbAnnotationSession
	 */
	public function fromObject($dbAnnotationSession)
	{
		parent::fromObject($dbAnnotationSession);
		
		$dbData = $dbAnnotationSession->getData();
		$this->data = new KalturaAnnotationSession();
		
		if($this->data && $dbData)
			$this->data->fromObject($dbData);
	}
	
	/**
	 * @param AnnotationSession $dbAnnotationSession
	 * @param array $propsToSkip
	 * @return AnnotationSession
	 */
	public function toInsertableObject($dbAnnotationSession = null, $propsToSkip = array())
	{
		if(is_null($dbAnnotationSession))
			$dbAnnotationSession = new AnnotationSession();
			
		return parent::toInsertableObject($dbAnnotationSession, $propsToSkip);
	}
}
