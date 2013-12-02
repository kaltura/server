<?php

/**
 * Abstract class for all engines that are based on a default list service
 */
abstract class KObjectFilterServiceEngine extends KObjectFilterEngineBase
{
	/**
	 * @return string
	 */
	abstract function getServiceId();

	/**
	 * @return string
	 */
	abstract function getServiceName();

	/**
	 * @return string
	 */
	abstract function getActionName();

	/**
	 * @return KalturaBaseService
	 */
	abstract function getServiceInstance();

	/**
	 * @param KalturaFilter $objectFilter
	 * @return array
	 */
	public function query(KalturaFilter $objectFilter)
	{
		$serviceInstance = $this->getServiceInstance();
		$serviceId = $this->getServiceId();
		$serviceName = $this->getServiceName();
		$actionName = $this->getActionName();
		$serviceInstance->initService($serviceId, $serviceName, $actionName);
		$pager = $this->getPager();

		$serviceActionItem = KalturaServicesMap::retrieveServiceActionItem($serviceId, $actionName);
		$actionMethodName = $serviceActionItem->actionMap[strtolower($actionName)]['actionMethodName'];
		return $serviceInstance->$actionMethodName($objectFilter, $pager);
	}

	protected function getPager()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = $this->getPageSize();
		$pager->pageIndex = $this->getPageIndex();
		return $pager;
	}
}