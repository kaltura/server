<?php
$url = 'https://catalog.video.msn.com/admin/services/videobyuuid.aspx?uuid=b4a3369d-bfe8-4737-a0b8-e4f9ac80fc24';
$username = 'Jonathan.Kanariek@kaltura.com';
$password = 'aMdlW2Cf';

$ch = curl_init();

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_FORBID_REUSE, true); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, false);

$results = curl_exec($ch);
if(!$results)
{
	$errNumber = curl_errno($ch);
	$errDescription = curl_error($ch);
	
	curl_close($ch);

	echo "$errNumber: $errDescription\n\n";
	throw new Exception($errDescription, $errNumber);
}
curl_close($ch);

var_dump($results);
$xml = new DOMDocument();
if(!$xml->loadXML($results))
	return;
	
$publishStateAttr = $xml->documentElement->attributes->getNamedItem('publishState');
if(!$publishStateAttr)
	return false;
	
$publishState = $publishStateAttr->value;
		
