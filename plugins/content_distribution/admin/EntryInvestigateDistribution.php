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
		$distributionFileSyncs = array();
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
		
		$distributionIds = array();
		foreach($distributions as $distribution)
		{
			$distributionFileSyncs[$distribution->id] = array();
			$distributionIds[] = $distribution->id;
		}
	
		if(count($distributionIds))
		{
			try
			{
				$filter = new KalturaFileSyncFilter();
				$filter->fileObjectTypeEqual = KalturaFileSyncObjectType::ENTRY_DISTRIBUTION;
				$filter->objectIdIn = implode(',', $distributionIds);
				
				$pager = new KalturaFilterPager();
				$pager->pageSize = 100;
				
				$fileSyncList = $client->fileSync->listAction($filter, $pager);
				$fileSyncs = $fileSyncList->objects;
				foreach($fileSyncs as $fileSync)
					$distributionFileSyncs[$fileSync->objectId][] = $fileSync;			
			}
			catch (Exception $e)
			{
				$errDescription = $e->getMessage();
			}
		}
		
		return array(
			'distributions' => $distributions,
			'distributionFileSyncs' => $distributionFileSyncs,
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