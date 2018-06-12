<?php
/**
 * Core class representing the finalized implicit recipient list provider to be passed into the batch mechanism
 * 
 * @package plugins.emailNotification
 * @subpackage model.data 
 */
class kEmailNotificationGroupRecipientJobData extends kEmailNotificationRecipientJobData
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

}