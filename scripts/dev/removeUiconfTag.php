<?php

if ($argc != 3) {
	echo 'Usage {folder path} {tabs to remove}
For example:
	php removeUiconfTag.php /web/content/uiconf YouTube,MySpace
';
	exit ();
}

$dirPath = $argv [1];
$tabNames = explode ( ',', $argv [2] );
foreach($tabNames as $index => $tabName)
	$tabNames[$index] = strtolower($tabName);

removeTabsFromFolder ( $dirPath, $tabNames );

function removeTabsFromFolder($dirPath, $tabs) 
{
	$d = dir ( $dirPath );
	while ( false !== ($entry = $d->read ()) ) 
	{
		if($entry == '.' || $entry == '..')
			continue;
			
		$currentPath = "$dirPath/$entry";
		if(is_dir($currentPath))
		{
			removeTabsFromFolder($currentPath, $tabs);
		}
		elseif(is_xml($currentPath))
		{
			removeTabsFromFile($currentPath, $tabs);
		}
	}
	$d->close ();
}

function is_xml($path) 
{
	if(pathinfo($path, PATHINFO_EXTENSION) != 'xml')
		return false;
		
//	if(mime_content_type($path) != 'text/html')
//		return false;
	
	return true;
}

function removeTabsFromFile($xmlPath, $tabs) 
{
	$xmlDoc = new DOMDocument ();
	@$xmlDoc->load ( $xmlPath );
	if(!$xmlDoc)
		return;
	
//	echo "Validates $xmlPath\n";
	$somethingRemoved = false;
	
	$services = $xmlDoc->getElementsByTagName ( 'service' );
	if ($services->length)
	{
		foreach ( $services as $service ) 
		{
			$serviceName = getServiceName ( $service );
			if (! in_array ( $serviceName, $tabs ))
				continue;
			
			$somethingRemoved = true;
			echo "\t$serviceName removed from $xmlPath\n";
			$serviceParent = $service->parentNode;
			$serviceParent->removeChild ( $service );
		}
	}

	$providers = $xmlDoc->getElementsByTagName ( 'provider' );
	if ($providers->length)
	{	
		foreach ( $providers as $provider ) 
		{
			$providerName = strtolower($provider->getAttribute('name'));
			$providerId = strtolower($provider->getAttribute('id'));
			if (! in_array ( $providerName, $tabs ) && ! in_array ( $providerId, $tabs ))
				continue;
			
			$somethingRemoved = true;
			echo "\t$providerName removed from $xmlPath\n";
			$providerParent = $provider->parentNode;
			$providerParent->removeChild ( $provider );
		}
	}
	
	if($somethingRemoved)
		$xmlDoc->save ( $xmlPath );
}

function getServiceName(DOMElement $service) 
{
	$namesElement = $service->getElementsByTagName ( 'name' );
	$nameElement = $namesElement->item ( 0 );
	return strtolower($nameElement->nodeValue);
}
