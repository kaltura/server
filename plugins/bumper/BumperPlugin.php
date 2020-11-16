<?php
/**
 * @package plugins.bumper
 */
class BumperPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaPlaybackContextDataContributor
{
	const PLUGIN_NAME = 'bumper';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	public static function dependsOn()
	{
		$eSearchDependency = new KalturaDependency(ElasticSearchPlugin::getPluginName());
		return array($eSearchDependency);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'bumper' => 'BumperService',
		);
		return $map;
	}

	/**
	 * Receives the context-data result and adds an instance of KalturaPluginData to the pluginData containing
	 * the specific plugins context-data.
	 *
	 * @param entry $entry
	 * @param kPlaybackContextDataParams $entryPlayingDataParams
	 * @param kPlaybackContextDataResult $result
	 * @param kContextDataHelper $contextDataHelper
	 * @param string $type
	 */
	public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper)
	{
		if ($entryPlayingDataParams->getType() == self::getPluginName())
		{
			$bumperData = array();
			$dbBumper = kBumper::getBumperData($entry);
			if($dbBumper && $dbBumper->getEntryId() && $dbBumper->getUrl())
			{
				$bumper = new KalturaBumper();
				$bumper->fromObject( $dbBumper );
				$bumperData[] = $bumper;
			}
			$result->setBumperData($bumperData);
		}
	}

	/**
	 * @param $streamerType
	 * @return boolean
	 */
	public function isSupportStreamerTypes($streamerType)
	{
		return false;
	}

	/**
	 * @param $drmProfile
	 * @param $scheme
	 * @param $customDataObject
	 * @return boolean
	 */
	public function constructUrl($drmProfile, $scheme, $customDataObject)
	{
		return '';
	}
}