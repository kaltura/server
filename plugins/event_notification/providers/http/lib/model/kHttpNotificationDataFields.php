<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.data
 */
class kHttpNotificationDataFields extends kHttpNotificationData
{
	/**
	 * Contains the calculated data to be sent
	 * @var string
	 */
	protected $data;
	
	/* (non-PHPdoc)
	 * @see kHttpNotificationData::setScope()
	 */
	public function setScope(kScope $scope)
	{
		$this->data = http_build_query($scope->getDynamicValues());
	}
	
	/**
	 * Returns the calculated data
	 * @return string
	 */
	public function getData() 
	{
		return $this->data;
	}	
}