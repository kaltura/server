<?php
/**
 * Core class for a provider for the recipients of category-related notifications.
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class EmailNotificationCategoryRecipientsProvider extends EmailNotificationRecipientProvider
{
	/**
	 * Additional permissions for the category_users of the category (the users with these permissions will be added in the CC of the email notification)
	 * @var string
	 */
	protected $cc_permissions;
	
	/**
	 * Additional permissions for the category_users of the category (the users with these permissions will be added in the BCC of the email notification)
	 * @var string
	 */
	protected $bcc_permissions;
	
	/**
	 * ID of the category to whose subscribers the email should be sent
	 * @var int
	 */
	protected $category_id;
	/**
	 * @return the $additional_permissions
	 */
	public function getAdditionalPermissions() {
		return $this->additional_permissions;
	}

	/**
	 * @param field_type $additional_permissions
	 */
	public function setAdditionalPermissions($additional_permissions) {
		$this->additional_permissions = $additional_permissions;
	}

	/**
	 * @return the $category_id
	 */
	public function getCategoryId() {
		return $this->category_id;
	}

	/**
	 * @param field_type $category_id
	 */
	public function setCategoryId($category_id) {
		$this->category_id = $category_id;
	}

	
	
}