<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaCustomizedEmailContents extends KalturaObject
{
	/**
	 * The subject of the customized email
	 * @var string
	 */
	public $emailSubject;
	
	/**
	 * The body of the customized email
	 * @var string
	 */
	public $emailBody;
	
	/**
	 * @var string
	 */
	public $baseLink;
	
	private static $map_between_objects = array
	(
		'emailSubject',
		'emailBody',
		'baseLink',
	);
	
	public function getMapBetweenObjects ()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject ($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new kCustomizedEmailContents();
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function setEmailSubject ($emailSubject)
	{
		$this->emailSubject = $emailSubject;
	}
	
	public function setEmailBody ($emailBody)
	{
		$this->emailBody = $emailBody;
	}
	
	public function setBaseLink($baseLink)
	{
		$this->baseLink = $baseLink;
	}
	
	public function getEmailSubject ()
	{
		return $this->emailSubject;
	}
	
	public function getEmailBody ()
	{
		return $this->emailBody;
	}
	
	public function getBaseLink ()
	{
		return $this->baseLink;
	}
	

}