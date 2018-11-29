<?php
/**
 * @package plugins.beacon
 * @subpackage model.search
 */
class kScheduledResourceSearch extends kBaseSearch
{
	public function doSearch(ESearchOperator $eSearchOperator, $statuses = array(), $objectId, kPager $pager = null,
							 ESearchOrderBy $order = null)
	{
		return null;
	}

	public function getPeerName()
	{
		// TODO: Implement getPeerName() method.
	}

	public function getPeerRetrieveFunctionName()
	{
		// TODO: Implement getPeerRetrieveFunctionName() method.
	}

	public function getElasticTypeName()
	{
		// TODO: Implement getElasticTypeName() method.
	}
}
