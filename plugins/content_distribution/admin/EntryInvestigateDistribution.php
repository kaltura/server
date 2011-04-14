<?php
class Kaltura_View_Helper_EntryInvestigateDistribution extends Kaltura_View_Helper_EntryInvestigatePlugin
{
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getDataArray()
	 */
	public function getDataArray($entryId, $partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		$contentDistributionPlugin = Kaltura_Client_ContentDistribution_Plugin::get($client);
		$fileSyncPlugin = Kaltura_Client_FileSync_Plugin::get($client);
		
		if(!$client)
		{
			$errors[] = 'init client failed';
			return;
		}
		
		$filter = new Kaltura_Client_ContentDistribution_Type_EntryDistributionFilter();
		$filter->entryIdEqual = $entryId;
		
		$distributions = array();
		$distributionFileSyncs = array();
		$errDescription = null;
		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			$entryDistributionList = $contentDistributionPlugin->entryDistribution->listAction($filter);
			Infra_ClientHelper::unimpersonate();
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
				$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
				$filter->fileObjectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION;
				$filter->objectIdIn = implode(',', $distributionIds);
				
				$pager = new Kaltura_Client_Type_FilterPager();
				$pager->pageSize = 100;
				
				$fileSyncList = $fileSyncPlugin->fileSync->listAction($filter, $pager);
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