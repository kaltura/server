<?php
/**
 * Core class for recipient provider which provides a dynamic list of user recipients based on filter.
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationGroupRecipientProvider extends kEmailNotificationRecipientProvider
{
	/**
	 * @var string
	 */
	protected $groupId;
	
	/**
	 * @return string $groupId
	 */
	public function getGroupId() {
		return $this->groupId;
	}

	/**
	 * @param string $groupId
	 */
	public function setGroupId($groupId) {
		$this->groupId = $groupId;
	}
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::getScopedProviderJobData()
	 */
	public function getScopedProviderJobData(kScope $scope = null) 
	{
		$ret = new kEmailNotificationGroupRecipientJobData();
		$ret->setGroupId($this->groupId);
		return $ret;
	}
	


	
}