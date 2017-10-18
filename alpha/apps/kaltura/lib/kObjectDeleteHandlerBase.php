<?php

abstract class kObjectDeleteHandlerBase {
    /**
     * @param string $id
     * @param int $type
     */
    protected function syncableDeleted($id, $type)
    {
        $c = new Criteria();
        $c->add(FileSyncPeer::OBJECT_ID, $id);
        $c->add(FileSyncPeer::OBJECT_TYPE, $type);
        $c->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);

        $fileSyncs = FileSyncPeer::doSelect($c);
        foreach($fileSyncs as $fileSync)
        {
            $key = kFileSyncUtils::getKeyForFileSync($fileSync);
            kFileSyncUtils::deleteSyncFileForKey($key);
        }
    }
}