<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.data
 */
abstract class kHttpNotificationData
{
	/**
	 * Content Type
	 * @var int
	 */
	protected $contentType;

	public function setContentType($contentType)
	{
		$this->contentType = $contentType;
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * Applies scope upon creation
	 * @param kScope $scope
	 */
	abstract public function setScope(kScope $scope);
}