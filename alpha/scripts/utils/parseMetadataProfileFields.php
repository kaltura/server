<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php parseMetadataProfileFields.php {metadata profile id}\n";
	exit;
} 
$metadataProfileId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

$metadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
if(!$metadataProfile)
{
	echo "Metadata Profile not found.\n";
	exit;
}

kMetadataManager::parseProfileSearchFields($metadataProfile);
