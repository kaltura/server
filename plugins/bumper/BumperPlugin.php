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
			if($dbBumper && $dbBumper->getEntryId())
			{
				$dbBumperEntry = entryPeer::retrieveByPK($dbBumper->getEntryId());
				if ($dbBumperEntry)
				{
					$bumper = new KalturaBumper();
					$bumper->fromObject( $dbBumper );

					$bumperContextDataHelper = new kContextDataHelper($dbBumperEntry, $dbBumperEntry->getPartner(), null);
					if ($dbBumperEntry->getAccessControl() && $dbBumperEntry->getAccessControl()->hasRules())
					{
						$accessControlScope = $dbBumperEntry->getAccessControl()->getScope();
					}
					else
					{
						$accessControlScope = new accessControlScope();
					}
					$bumperContextDataHelper->buildContextDataResult($accessControlScope, kContextDataHelper::ALL_TAGS, null, null, true);
					if ($bumperContextDataHelper->getDisableCache())
					{
						KalturaResponseCacher::disableCache();
					}

					$bumperContextDataHelper->setMediaProtocol(null);
					$bumperContextDataHelper->setStreamerType(null);

					$playbackContextDataHelper = new kPlaybackContextDataHelper();
					$playbackContextDataHelper->constructPlaybackContextResult($bumperContextDataHelper, $dbBumperEntry);

					$bumperResult = new KalturaPlaybackContext();
					$bumperResult->fromObject($playbackContextDataHelper->getPlaybackContext());

					$bumper->sources = $bumperResult->sources;

					$bumperData[] = $bumper;
				}
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
