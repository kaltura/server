<?php
/**
 * API object which provides the recipients of category related notifications.
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationCategoryRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * The ID of the category whose subscribers should receive the email notification.
	 * @var KalturaStringValue
	 */
	public $categoryId;


} 