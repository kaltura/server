<?php
ini_set("memory_limit","1024M");
require_once(__DIR__ . '/../bootstrap.php');

$metadataProfileId = $argv[1];
$objectType = $argv[2];

$filter = new MetadataFilter();
$filter->set('_eq_status', 1);
$filter->set('_eq_object_type', $objectType);
$filter->set('_eq_metadata_profile_id', $metadataProfileId);

$createdAtOffset = 0;
do{
    $c = new Criteria();
    $c->addAscendingOrderByColumn(MetadataPeer::CREATED_AT);
    if($createdAtOffset)
    {
        $filter->set('_gte_created_at', $createdAtOffset);
    }
    $filter->attachToCriteria($c);

    $c->setLimit( 500 );

    $result = MetadataPeer::doSelect($c);

    foreach ($result as $metadataObj)
    {
        /* @var $metadataObj Metadata */
        $fileSyncKey = $metadataObj->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
        kFileSyncUtils::file_put_contents();
    }

    $createdAtOffset = count($result) ?  $metadataObj->getCreatedAt() +1 : 0;

}while(count($result));