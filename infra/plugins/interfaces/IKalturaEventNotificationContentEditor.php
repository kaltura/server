<?php
/**
 * Interface which allows plugin to add its own content to the playManifest action output.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEventNotificationContentEditor extends IKalturaBase
{
	/**
	 * Function sweeps the given fields of the emailNotificationTemplate, and parses expressions of the type
	 * {metadata:[metadataProfileSystemName]:[metadataProfileFieldSystemName]}
	 * 
	 * @param array $sweepFieldValues
	 * @param kScope $scope
	 * @param int $objecType
	 * @return array
	 */
	public static function editTemplateFields($sweepFieldValues, $scope, $objecType);
}
