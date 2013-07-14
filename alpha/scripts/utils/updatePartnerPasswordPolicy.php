<?php

require_once (dirname ( __FILE__ ) . '/../bootstrap.php');
myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;

if($argc < 5)
{
	echo 'Arguments missing.'.PHP_EOL;
	echo 'Usage: php '.__FILE__.' [partner id] [maxLoginAttempts] [passReplaceFreq] [numPrevPassToKeep] '.PHP_EOL;
	exit;
} 

$partnerId=$argv[1];
$maxLoginAttempts=$argv[2];
$passReplaceFreq=$argv[3];// in seconds
$numPrevPassToKeep=$argv[4];
$passwordPolicy = array();// a two two dimensions, first cell contains a regex and the second contains a description
//example:
//$passwordPolicy[] = array('',"- Must not contain your first or last name\n");
//$passwordPolicy[] = array("/^.{8,}$/","- Must contain at least 8 characters.\n");
//$passwordPolicy[] = array("/[0-9]+/","- Must contain at least one digit (0-9).\n");
//$passwordPolicy[] = array("/[a-z]+/","- Must contain at least one lowercase letter (a-z).\n");
//$passwordPolicy[] = array("/[~!@#$%^*=+?\(\)\-\[\]\{\}]+/","- Must contain at least one of the following symbols:  %%~!@#\$^*=+?[]{}.\n");
//$passwordPolicy[] = array("/^[^<>]*$/","- Must not contain the following characters: &lt; or &gt;.");


$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner){
	die ("partner [$partnerId] not found".PHP_EOL);
}
$partner->setPasswordStructureValidations($passwordPolicy);
$partner->setMaxLoginAttempts($maxLoginAttempts);
$partner->setPassReplaceFreq($passReplaceFreq);
$partner->setNumPrevPassToKeep($numPrevPassToKeep);

$partner->save();

echo "Done." . PHP_EOL;