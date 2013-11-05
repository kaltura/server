<?php
/**
 * Interface which allows plugin to add its own content to the playManifest action output.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEmailNotificationContentEditor extends IKalturaBase
{
	/**
	 * Function sweeps the given fields of the emailNotificationTemplate, and parses expressions of the type
	 * {metadata:[metadataProfileSystemName]:[metadataProfileFieldSystemName]}
	 * 
	 * @param EmailNotificationTemplate $emailNotificationTemplate
	 * @param kScope $scope
	 * @return array
	 */
	public static function editTemplateFields($emailNotificationTemplate, $scope);
}
