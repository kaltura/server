<?php
class Kaltura_View_Helper_FileSyncSubTypeTranslate extends Zend_View_Helper_Abstract
{
	private function entrySubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_ENTRY_SUB_TYPE_DATA = 1;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DATA');
				
			case 2: // FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT = 2;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DATA_EDIT');
				
			case 3: // FILE_SYNC_ENTRY_SUB_TYPE_THUMB = 3;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::THUMB');
				
			case 4: // FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE = 4;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::ARCHIVE');
				
			case 5: // FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD = 5;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DOWNLOAD');
				
			default:
				return $this->view->translate("unknown entry sub type [$objectSubType]");
		}
	}
	
	private function uiConfSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_UICONF_SUB_TYPE_DATA = 1;
				return $this->view->translate('KalturaFileSyncObjectType::UICONF::DATA');
				
			case 2: // FILE_SYNC_UICONF_SUB_TYPE_FEATURES = 2;
				return $this->view->translate('KalturaFileSyncObjectType::UICONF::FEATURES');
				
			default:
				return $this->view->translate("unknown ui_conf sub type [$objectSubType]");
		}
	}
	
	private function batchJobSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV = 1;
				return $this->view->translate('KalturaFileSyncObjectType::BATCHJOB::BULKUPLOADCSV');
				
			case 2: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG = 2;
				return $this->view->translate('KalturaFileSyncObjectType::BATCHJOB::BULKUPLOADLOG');
				
			default:
				return $this->view->translate("unknown batch sub type [$objectSubType]");
		}
	}
	
	private function flavorAssetSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET = 1;
				return $this->view->translate('KalturaFileSyncObjectType::FLAVOR_ASSET::ASSET');
				
			case 2: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG = 2;
				return $this->view->translate('KalturaFileSyncObjectType::FLAVOR_ASSET::CONVERT_LOG');
				
			default:
				return $this->view->translate("unknown asset sub type [$objectSubType]");
		}
	}
	
	private function flavorMetadataSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_METADATA_DATA = 1;
				return $this->view->translate('KalturaFileSyncObjectType::METADATA::DATA');
				
			default:
				return $this->view->translate("unknown metadata sub type [$objectSubType]");
		}
	}
	
	private function flavorEntryDistributionSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS = 1;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::SUBMIT_RESULTS');
				
			case 2: // FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS = 2;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::UPDATE_RESULTS');
				
			case 3: // FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS = 3;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::DELETE_RESULTS');
				
			case 4: // FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA = 4;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::SUBMIT_DATA');
				
			case 5: // FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA = 5;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::UPDATE_DATA');
				
			case 6: // FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA = 6;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY_DISTRIBUTION::DELETE_DATA');
				
			default:
				return $this->view->translate("unknown entry distribution sub type [$objectSubType]");
		}
	}
	
	public function fileSyncSubTypeTranslate($objectType, $objectSubType)
	{
		switch($objectType)
		{
			case KalturaFileSyncObjectType::ENTRY:
				return $this->entrySubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::UICONF:
				return $this->uiConfSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::BATCHJOB:
				return $this->batchJobSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::FLAVOR_ASSET:
				return $this->flavorAssetSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::METADATA:
				return $this->flavorMetadataSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::ENTRY_DISTRIBUTION:
				return $this->flavorEntryDistributionSubTypeTranslate($objectSubType);
				
			default:
				return $this->view->translate("unknown object type [$objectType]");
		}
	}
}