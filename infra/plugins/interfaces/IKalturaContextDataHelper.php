<?php
/**
 * Plugin interface which allows a plugin to determine content's defautl streamer and media types.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaContextDataHelper extends IKalturaBase
{
	public function getContextDataStreamerType ();
	
	public function getContextDataMediaProtocol ();
}
