<?php
class kKontikiUrlManager extends kUrlManager
{
    public function getFileSyncUrl(FileSync $fileSync, $tokenizeUrl = true)
    {
        $urn = $this->doGetFileSyncUrl($fileSync);
        return $urn;
    }
    
    protected function doGetFileSyncUrl(FileSync $fileSync)
    {
        $storageProfile = StorageProfilePeer::retrieveByPK($this->storageProfileId);
		/* @var $storageProfile KontikiStorageProfile */
		$kontikiAPIWrapper = new KontikiAPIWrapper($storageProfile->getStorageUrl());
        $playbackResource = $kontikiAPIWrapper->getPlaybackResource(KontikiPlugin::SERVICE_TOKEN_PREFIX.base64_encode($storageProfile->getServiceToken()), $fileSync->getFilePath());
		if (!$playbackResource) 
		{
			return null;
		}
		
		return strval($playbackResource->urn) . ";realmId:" . strval($playbackResource->realmId) . ";realmTicket:" .strval($playbackResource->realmTicket);
    }
}
