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
	
	public function setScope(kScope $scope = null)
	{
		if($this->content instanceof kStringField)
			$this->content->setScope($scope);
	}
}