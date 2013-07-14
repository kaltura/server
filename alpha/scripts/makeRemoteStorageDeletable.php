<?php
ini_set("memory_limit","256M");

require_once(__DIR__ . '/bootstrap.php');

if ($argc < 2)
{
    die("Script requires 2 parameters: valid storage profile ID and the value of the 'allow_auto_delete' property for the storage profile.\r\n");
}

$storageProfileId = $argv[1];

$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);


if ($argc < 3)
{
    echo 'No value specified. Setting remote storage deletion policy to false.\r\n';
    $autoDeletePolicy = false;
}
else
{
    if ($argv[2] == "true")
    {
        $autoDeletePolicy = true;
    }
    else
    {
        $autoDeletePolicy = false;
    }
}

if (!$storageProfile)
{
    die('Invalid storage profile ID provided.\n');
}

$storageProfile->setAllowAutoDelete(true);
$storageProfile->save();

echo "Storage Profile with ID <$storageProfileId> auto delete policy set to $autoDeletePolicy.\r\n";