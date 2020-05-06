<?php
/**
 * @package plugins.interactivity
 */
class InteractivityPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaExceptionHandler, IKalturaEnumerator
{
	const PLUGIN_NAME = 'interactivity';
	const INTERACTIVITY_CORE_EXCEPTION = 'kInteractivityException';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function getCapabilityCoreValue()
	{
		return kPluginableEnumsManager::apiToCore(KalturaEntryCapability::getEnumClass(), self::PLUGIN_NAME . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
		{
			return array('InteractivityEntryCapability');
		}

		if ($baseEnumName == KalturaEntryCapability::getEnumClass())
		{
			return array('InteractivityEntryCapability');
		}

		return array();
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
		$dependency = new KalturaDependency(FileSyncPlugin::getPluginName());
		return array($dependency);
	}

	public static function getServicesMap()
	{
		return array(
			'interactivity' => 'InteractivityService',
			'volatileInteractivity' => 'VolatileInteractivityService',
		);
	}

	/**
	 * @param $exception
	 * @return KalturaAPIException|null
	 * @throws Exception
	 */
	public static function handleInteractivityException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kInteractivityException::DUPLICATE_INTERACTIONS_IDS:
				$object = new KalturaAPIException(KalturaInteractivityErrors::DUPLICATE_INTERACTIONS_IDS);
				break;
			case kInteractivityException::DUPLICATE_NODES_IDS:
				$object = new KalturaAPIException(KalturaInteractivityErrors::DUPLICATE_NODES_IDS);
				break;
			case kInteractivityException::EMPTY_INTERACTIVITY_DATA:
				$object = new KalturaAPIException(KalturaInteractivityErrors::EMPTY_INTERACTIVITY_DATA);
				break;
			case kInteractivityException::DIFFERENT_DATA_VERSION:
				$object = new KalturaAPIException(KalturaInteractivityErrors::DIFFERENT_DATA_VERSION, $data[kInteractivityErrorMessages::VERSION_PARAMETER]);
				break;
			case kInteractivityException::MISSING_MANDATORY_PARAMETERS:
				$object = new KalturaAPIException(KalturaInteractivityErrors::MISSING_MANDATORY_PARAMETER, $data[kInteractivityErrorMessages::MISSING_PARAMETER]);
				break;
			case kInteractivityException::ILLEGAL_FIELD_VALUE:
				$object = new KalturaAPIException(KalturaInteractivityErrors::ILLEGAL_FIELD_VALUE, $data[kInteractivityErrorMessages::ERR_MSG]);
				break;
			case kInteractivityException::ENTRY_ILLEGAL_NODE_NUMBER:
				$object = new KalturaAPIException(KalturaInteractivityErrors::ENTRY_ILLEGAL_NODE_NUMBER);
				break;
			case kInteractivityException::ILLEGAL_ENTRY_NODE_ENTRY_ID:
				$object = new KalturaAPIException(KalturaInteractivityErrors::ILLEGAL_ENTRY_NODE_ENTRY_ID);
				break;
			case kInteractivityException::UNSUPPORTED_PLAYLIST_TYPE:
				$object = new KalturaAPIException(KalturaInteractivityErrors::UNSUPPORTED_PLAYLIST_TYPE);
				break;
			case kInteractivityException::CANT_UPDATE_NO_DATA:
				switch($data[kInteractivityErrorMessages::TYPE_PARAMETER])
				{
					case kEntryFileSyncSubType::INTERACTIVITY_DATA:
						$object = new KalturaAPIException(KalturaInteractivityErrors::NO_INTERACTIVITY_DATA, $data[kInteractivityErrorMessages::ENTRY_ID]);
						break;
					case kEntryFileSyncSubType::VOLATILE_INTERACTIVITY_DATA:
						$object = new KalturaAPIException(KalturaInteractivityErrors::NO_VOLATILE_INTERACTIVITY_DATA, $data[kInteractivityErrorMessages::ENTRY_ID]);
						break;
				}
				break;
			default:
				$object = null;
		}

		return $object;
	}

	public function getExceptionMap()
	{
		return array(
			self::INTERACTIVITY_CORE_EXCEPTION => array('InteractivityPlugin', 'handleInteractivityException'),
		);
	}
}