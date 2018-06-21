<?php
/**
 * @package api
 * @subpackage filters
 */

class MapFilterToList
{

	const LIST_ACTION = 'listAction';

	/**
	 * @param KalturaRelatedFilter $relatedFilter
	 * @return bool
	 * @throws Exception
	 */
	public static function validateAccess($relatedFilter)
	{
		$clazz = self::getServiceClassInstance($relatedFilter);
		return self::validate($clazz);
	}

	/**
	 * @param $relatedFilter
	 * @return string $clazz
	 * @throws Exception
	 */
	private static function getServiceClassInstance($relatedFilter)
	{
		$filterName = get_class($relatedFilter);
		$cleanName = self::removeAppendixes($filterName);
		$clazz = $cleanName . 'Service';
		/** currently we will not block any response profile that does not have list action on his service */
		if (!class_exists($clazz) || !method_exists($clazz, self::LIST_ACTION)) {
			$e = new KalturaAPIException (APIErrors::SERVICE_FORBIDDEN, 'Service class:  ' . $clazz . 'Not Found');
			header("X-Kaltura:error-" . $e->getCode());
			header("X-Kaltura-App: exiting on error " . $e->getCode() . " - " . $e->getMessage());
			throw $e;
		}
		return $clazz;
	}

	/**
	 * @param string $filterName
	 * @return string
	 */
	private static function removeAppendixes($filterName)
	{
		$wordList = array('Kaltura', 'BaseFilter', 'Filter');
		return str_replace($wordList, '', $filterName);
	}

	/**
	 * @param string $clazz
	 * @return bool
	 * @throws Exception
	 */
	private static function validate($clazz)
	{
		try {
			/** @var KalturaBaseService $service */
			$service = new $clazz();
			list($serviceId, $serviceName) = KalturaServicesMap::getServiceIdAndServiceNameByClass($clazz);
			$service->initService($serviceId, $serviceName, 'list');
		} catch (Exception $e) {
			KalturaLog::INFO('Response Profile Validation Access Failed For Class: ' . $clazz);
			if (is_a($e, 'KalturaAPIException'))
				throw  $e;
			KalturaLog::err($e);
			return false;
		}
		return true;
	}

}