<?php
/**
 * @package Admin
 * @subpackage views
 */
class Kaltura_View_Helper_FileSyncSubTypeTranslate extends Zend_View_Helper_Abstract
{
	private function entrySubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_ENTRY_SUB_TYPE_DATA = 1;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::DATA');
				
			case 2: // FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT = 2;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::DATA_EDIT');
				
			case 3: // FILE_SYNC_ENTRY_SUB_TYPE_THUMB = 3;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::THUMB');
				
			case 4: // FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE = 4;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::ARCHIVE');
				
			case 5: // FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD = 5;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::DOWNLOAD');
				
			case 6: // FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB = 6;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::OFFLINE_THUMB');
				
			case 7: // FILE_SYNC_ENTRY_SUB_TYPE_ISM = 7;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::ISM');
				
			case 8: // FILE_SYNC_ENTRY_SUB_TYPE_ISMC = 8;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::ISMC');
				
			case 9: // FILE_SYNC_ENTRY_SUB_TYPE_CONVERSION_LOG = 9;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::CONVERSION_LOG');
				
			case 10: // FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY = 10;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::LIVE_PRIMARY');
				 
			case 11: // FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY = 11;
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::LIVE_SECONDARY');

			case 12:
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::INTERACTIVITY_DATA');

			case 13:
				return $this->view->translate('Kaltura_Client_FileSync_Enum_FileSyncObjectType::ENTRY::VOLATILE_INTERACTIVITY_DATA');
	
			default:
				return $this->view->translate("unknown entry sub type [$objectSubType]");
		}
	}
	
	private function uiConfSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_UICONF_SUB_TYPE_DATA = 1;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::UICONF::DATA');
				
			case 2: // FILE_SYNC_UICONF_SUB_TYPE_FEATURES = 2;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::UICONF::FEATURES');
				
			default:
				return $this->view->translate("unknown ui_conf sub type [$objectSubType]");
		}
	}
	
	private function batchJobSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV = 1;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::BATCHJOB::BULKUPLOADCSV');
				
			case 2: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG = 2;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::BATCHJOB::BULKUPLOADLOG');
				
			default:
				return $this->view->translate("unknown batch sub type [$objectSubType]");
		}
	}
	
	private function flavorAssetSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET = 1;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET::ASSET');
				
			case 2: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG = 2;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET::CONVERT_LOG');
				
			case 3: // FILE_SYNC_ASSET_SUB_TYPE_CONVERT_ISM = 3;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET::ISM');
				
			case 4: // FILE_SYNC_ASSET_SUB_TYPE_ISMC = 4;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET::ISMC');
								
			default:
				return $this->view->translate("unknown asset sub type [$objectSubType]");
		}
	}
	
	private function flavorMetadataSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_METADATA_DATA = 1;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::METADATA::DATA');
				
			default:
				return $this->view->translate("unknown metadata sub type [$objectSubType]");
		}
	}
	
	private function flavorEntryDistributionSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS = 1;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::SUBMIT_RESULTS');
				
			case 2: // FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS = 2;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::UPDATE_RESULTS');
				
			case 3: // FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS = 3;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::DELETE_RESULTS');
				
			case 4: // FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA = 4;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::SUBMIT_DATA');
				
			case 5: // FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA = 5;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::UPDATE_DATA');
				
			case 6: // FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA = 6;
				return $this->view->translate('Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION::DELETE_DATA');
				
			default:
				return $this->view->translate("unknown entry distribution sub type [$objectSubType]");
		}
	}
	
	public function fileSyncSubTypeTranslate($objectType, $objectSubType)
	{
		switch($objectType)
		{
			case Kaltura_Client_Enum_FileSyncObjectType::ENTRY:
				return $this->entrySubTypeTranslate($objectSubType);
				
			case Kaltura_Client_Enum_FileSyncObjectType::UICONF:
				return $this->uiConfSubTypeTranslate($objectSubType);
				
			case Kaltura_Client_Enum_FileSyncObjectType::BATCHJOB:
				return $this->batchJobSubTypeTranslate($objectSubType);
				
			case Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET:
				return $this->flavorAssetSubTypeTranslate($objectSubType);
				
			case Kaltura_Client_Enum_FileSyncObjectType::METADATA:
				return $this->flavorMetadataSubTypeTranslate($objectSubType);
				
			case Kaltura_Client_Enum_FileSyncObjectType::ENTRY_DISTRIBUTION:
				return $this->flavorEntryDistributionSubTypeTranslate($objectSubType);
				
			default:
				return $this->view->translate("unknown object type [$objectType]");
		}
	}
}