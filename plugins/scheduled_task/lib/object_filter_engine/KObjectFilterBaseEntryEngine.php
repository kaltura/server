<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
class KObjectFilterBaseEntryEngine extends KObjectFilterEngineBase
{
	/**
	 * @param KalturaFilter $filter
	 * @return array
	 */
	public function query(KalturaFilter $filter)
	{
		$coreFilter = $filter->toObject();
		if(ESearchEntryQueryFromFilter::canTransformFilter($coreFilter))
		{
			$ESearchAdapter = new ESearchEntryQueryFromFilter();
			$corePager = $this->getPager()->toObject();
			return 	$ESearchAdapter->retrieveElasticQueryEntryIds($coreFilter, $corePager);
		}

		return $this->_client->baseEntry->listAction($filter, $this->getPager());
	}
}