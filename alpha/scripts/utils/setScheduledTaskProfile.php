<?php
	$options = getopt('r:p:i:',array("status:","maxValues:"));
	
	print_r($options);
	if(!(isset($options['r']) && isset($options['p']) && isset($options['i'])) || !(isset($options['status']) || isset($options['maxValues'])))
		die("Usage: php ". basename(__FILE__) ." -r [realRun] -p [partner-id] -i [profile-id] --status {status} --maxValues {maxValues}" . PHP_EOL);

	require_once("/opt/kaltura/app/alpha/scripts/bootstrap.php");

	$dryRun= $options['r'] !== 'realRun';
	KalturaStatement::setDryRun($dryRun);
	if ($dryRun)
		KalturaLog::debug('>>>dry run --- in order to save, give real_run as a second parameter');
	
	$partnerId = $options['p'];
	$profileId = $options['i'];
	
	$profile = ScheduledTaskProfilePeer::retrieveByPK($profileId);
	if(!$profile)
		die("profile not found" . PHP_EOL);
	
	if($profile->getPartnerId() != $partnerId)
		die("wrong partner id - profile partner id value - " . $profile->getPartnerId() . PHP_EOL);

	KalturaLog::debug("profile before update - " . print_r($profile,true));
	
	$shouldUpdate = false;
	foreach ($options as $option => $value)
	{
		if ($option == 'status')
		{
			$statusRefClass = new ReflectionClass('ScheduledTaskProfileStatus');
			$statusConstants = $statusRefClass->getConstants();
			
			if(in_array($value, $statusConstants))
			{
				$profile->setStatus($value);
				$shouldUpdate = true;
			}
			else
			{
				KalturaLog::err("status value does not exist. allowed values - " . print_r($statusConstants, true));
			}
		}
			
		if ($option == 'maxValues')
		{
			$intValue = (int)($value);
			if($intValue > 0)
			{
				$profile->setMaxTotalCountAllowed($intValue);
				$shouldUpdate = true;
			}
			else
			{
				KalturaLog::err("negative 'max_values_allowed' value given");
			}
		}
	}
	
	if(!$shouldUpdate)
	{
		die("no update" . PHP_EOL);
	}
    
	$profile->save();

