<?php
class KalturaAnnotation extends KalturaObject implements IFilterable 
{
	//TODO
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
	 * @var KalturaAnnotationStatus
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
	( //TODO
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
	 * @param Annotation $dbAnnotation
	 * @param array $propsToSkip
	 * @return Annotation
	 */
	public function toObject($dbAnnotation = null, $propsToSkip = array())
	{
		if(is_null($dbAnnotation))
			$dbAnnotation = new Annotation();
			
		return parent::toObject($dbAnnotation, $propsToSkip);
	}

	/**
	 * @param Annotation $dbAnnotation
	 */
	public function fromObject($dbAnnotation)
	{
		parent::fromObject($dbAnnotation);
		
		$dbData = $dbAnnotation->getData();
		$this->data = new KalturaAnnotation();
		
		if($this->data && $dbData)
			$this->data->fromObject($dbData);
	}
	
	/**
	 * @param Annotation $dbAnnotation
	 * @param array $propsToSkip
	 * @return Annotation
	 */
	public function toInsertableObject($dbAnnotation = null, $propsToSkip = array())
	{
		if(is_null($dbAnnotation))
			$dbAnnotation = new Annotation();
			
		return parent::toInsertableObject($dbAnnotation, $propsToSkip);
	}
}
