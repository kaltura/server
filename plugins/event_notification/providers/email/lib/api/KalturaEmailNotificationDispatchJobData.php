<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.objects
 */
class KalturaEmailNotificationDispatchJobData extends KalturaEventNotificationDispatchJobData
{
	
	/**
	 * Define the email sender email
	 * @var string
	 */
	public $fromEmail;
	
	/**
	 * Define the email sender name
	 * @var string
	 */
	public $fromName;
	
	/**
	 * Define the email receipient email
	 * @var string
	 */
	public $toEmail;
	
	/**
	 * Define the email receipient name
	 * @var string
	 */
	public $toName;
	
	private static $map_between_objects = array
	(
		'fromEmail',
		'fromName',
		'toEmail',
		'toName',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
