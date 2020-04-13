<?php
/**
 * @package plugins.interactivity
 */
class InteractivityPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaExceptionHandler
{
	const PLUGIN_NAME = 'interactivity';
	const INTERACTIVITY_CORE_EXCEPTION = 'kInteractivityException';

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
		$dependency = new KalturaDependency(FileSyncPlugin::getPluginName());
		return array($dependency);
	}

	public static function getServicesMap ()
	{
		return array(
			'interactivity' => 'InteractivityService',
			'volatileInteractivity' => 'VolatileInteractivityService',
		);
	}

	public static function handleInteractivityException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
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