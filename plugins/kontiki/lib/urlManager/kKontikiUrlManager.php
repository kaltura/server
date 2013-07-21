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
		$kontikiAPIWrapper = new KontikiAPIWrapper($storageProfile->getApiEntryPoint());
        $urn = $kontikiAPIWrapper->getPlaybackUrn("srv-".base64_encode($storageProfile->getServiceToken()), $fileSync->getFilePath());
		if (!$urn) 
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY);
		}
		
		return $urn;
    }
}
