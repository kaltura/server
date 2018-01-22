<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class liveAsset extends flavorAsset
{
	public function getMulticastIP ()
	{
		return $this->getFromCustomData('multicast_ip');
	}
	
	public function setMulticastIP ($v)
	{
		$this->putInCustomData('multicast_ip', $v);
	}
	
	public function getMulticastPort ()
	{
		return $this->getFromCustomData('multicast_port');
	}
	
	public function setMulticastPort ($v)
	{
		$this->putInCustomData('multicast_port', $v);
	}

	public function getLiveSegmentVersion($index)
	{
		return $this->getFromCustomData("liveSegmentVersion-$index", null, 0);
	}
	
	public function incLiveSegmentVersion($index)
	{
		$subType = self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY;
		if($index == EntryServerNodeType::LIVE_BACKUP)
		{
			$subType = self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY;
		}
			
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getLiveSegmentVersion($index), FileSyncObjectType::ASSET, $subType);
		$this->putInCustomData("liveSegmentVersion-$index", $newVersion);
	}
	
	/* (non-PHPdoc)
	 * @see asset::validateFileSyncSubType($sub_type)
	 */
	protected static function validateFileSyncSubType($sub_type)
	{
		if(	$sub_type == self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY || $sub_type == self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY)
		{
			return true;
		}
		
		KalturaLog::log("Sub type provided [$sub_type] is not one of known live-asset sub types validating from parent");
		return parent::validateFileSyncSubType($sub_type);
	}
	
	/* (non-PHPdoc)
	 * @see asset::getVersionForSubType($sub_type, $version)
	 */
	protected function getVersionForSubType($sub_type, $version = null)
	{
		if($sub_type == self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY)
		{
			return $this->getLiveSegmentVersion(EntryServerNodeType::LIVE_PRIMARY);
		}
		
		if($sub_type == self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY)
		{
			return $this->getLiveSegmentVersion(EntryServerNodeType::LIVE_BACKUP);
		}
			
		return parent::getVersionForSubType($sub_type, $version);
	}

	/* (non-PHPdoc)
	 * @see asset::generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		if($sub_type != self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY && $sub_type != self::FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY)
		{
			return parent::generateFileName($sub_type, $version);
		}
		
		return $this->getEntryId() . "_" . $this->getId() . "_{$sub_type}_{$version}";
	}
	
	public function shouldEncrypt()
	{
		return false;
	}
}
