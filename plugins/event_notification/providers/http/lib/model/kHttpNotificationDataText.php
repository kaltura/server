<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.data
 */
class kHttpNotificationDataText extends kHttpNotificationData
{
	/**
	 * @var kStringValue
	 */
	protected $content;
	
	/**
	 * Contains the calculated data to be sent
	 * @var string
	 */
	protected $data;
	
	/**
	 * @return kStringValue $content
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param kStringValue $content
	 */
	public function setContent(kStringValue $content)
	{
		$this->content = $content;
	}
	
	/* (non-PHPdoc)
	 * @see kHttpNotificationData::setScope()
	 */
	public function setScope(kScope $scope)
	{
		if($this->content instanceof kStringField)
			$this->content->setScope($scope);
			
		$this->data = $this->content->getValue();
		
		$replace = $scope->getDynamicValues('{', '}');
		$search = array_keys($replace);
		$this->data = str_replace($search, $replace, $this->data);
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