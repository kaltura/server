<?php
/**
 * Plugin interface which allows a plugin to determine content's defautl streamer and media types.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaContextDataHelper extends IKalturaBase
{
	/**
	 * @param accessControlScope $scope
	 * @param string $flavorTags
	 * @param string $streamerType
	 */
	public function getContextDataStreamerType (accessControlScope $scope, $flavorTags, $streamerType);
	
	/**
	 * @param accessControlScope $scope
	 * @param string $flavorTags
	 * @param string $streamerType
	 * @param string $mediaProtocol
	 */
	public function getContextDataMediaProtocol (accessControlScope $scope, $flavorTags, $streamerType, $mediaProtocol);
}
