<?php

ini_set("memory_limit","1024M");
if ($argc < 3)
{
	die ('DRM Policy ID and signing key required.\n');
}

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

$drmPolicyId = $argv[1];
$signingKey = $argv[2];

shout("Adding signing key [".$signingKey."] to drm policy [".$drmPolicyId."]",true);

$drmDbPolicy = DrmPolicyPeer::retrieveByPK($drmPolicyId);
if (is_null($drmDbPolicy))
{
	throw new kCoreException("DRM Policy Id is invalid");
}
$drmDbPolicy->putInCustomData(DrmProfile::CUSTOM_DATA_SIGNING_KEY, $signingKey);
$drmDbPolicy->save();
shout("Finished adding signingkey to drm policy",true);
	
	//write to log function
	function shout($str,$newLine=false) 
	{
		echo "[".date('H:i:s')."] ". $str . ($newLine ? "\n" : ""); 
	}

?>
