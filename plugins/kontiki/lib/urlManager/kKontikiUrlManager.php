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
        $storageProfile = StorageProfilePeer::retrieveByPK($fileSync->getDc());
        KontikiAPIWrapper::$entryPoint = $this->storageProfileId->getApiEntryPoint();
        return KontikiAPIWrapper::getPlaybackUrn("srv-".base64_encode($this->storageProfile->getServiceToken()), $fileSync->getFilePath());
    }
}
