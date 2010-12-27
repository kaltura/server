<?php
class Kaltura_View_Helper_EntryInvestigateDistribution extends Kaltura_View_Helper_EntryInvestigatePlugin
{
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getDataArray()
	 */
	public function getDataArray($entryId, $partnerId)
	{
		$client = Kaltura_ClientHelper::getClient();
		if(!$client)
		{
			$errors[] = 'init client failed';
			return;
		}
		
		$filter = new KalturaEntryDistributionFilter();
		$filter->entryIdEqual = $entryId;
		
		$distributions = array();
		$errDescription = null;
		try
		{
			Kaltura_ClientHelper::impersonate($partnerId);
			$entryDistributionList = $client->entryDistribution->listAction($filter);
			Kaltura_ClientHelper::unimpersonate();
			$distributions = $entryDistributionList->objects;
		}
		catch (Exception $e)
		{
			$errDescription = $e->getMessage();
		}
		
		return array(
			'distributions' => $distributions,
			'errDescription' => $errDescription,
		);
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getTemplatePath()
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getPHTML()
	 */
	public function getPHTML()
	{
		return 'entry-investigate-distribution.phtml';
	}
}