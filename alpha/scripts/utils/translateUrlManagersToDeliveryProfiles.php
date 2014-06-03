<?php
/**
 * This script is responsible for basic transfomation of url managers to delivery profiles.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

/**
 * Parameters 
 * -------------- 
 */
$urlManagersFilename = dirname(__FILE__) . "/../../../configurations/url_managers.prod.ini";

// Set this flag if you really want to execute it.
$dryRun = true;

/**
 *	All these parameters are unsupported. If your configuration uses them -
 * you will have to configure them yourself. 
 */
$unsupportedToken = array("params.rtsp_cpcode",
    "params.secure_hd_auth_seconds",
    "params.secure_hd_auth_param",
    "params.secure_hd_auth_acl_regex",
    "params.secure_hd_auth_acl_postfix",
    "params.secure_hd_auth_salt",
	"params.auth_header_data",
	"params.auth_header_sign",
	"params.auth_header_timeout",
	"params.auth_header_salt",
	"params.http_auth_salt",
	"params.http_auth_param",
	"params.http_auth_seconds",
	"params.http_auth_root_dir",
	"params.rtmp_auth_salt",
	"params.rtmp_auth_profile",
	"params.rtmp_auth_type",
	"params.rtmp_auth_seconds",
	"params.rtmp_auth_aifp",
	"params.rtmp_auth_slist_find_prefix",
	"params.smooth_auth_seconds",
	"params.smooth_auth_param",
	"params.smooth_auth_salt"
);

/**
 * Functions
 * -------------- 
 */

function parseHierarchicalIniFile($fileName) {
	$urlManagersIni = parse_ini_file ( $fileName , true);
	
	$urlManagersInheritance = array();
	$urlManagers = array();
	foreach($urlManagersIni as $key => $urlManagerIni) {
		$pieces = explode(":", $key);
		$name = trim($pieces[0]);
		switch(count($pieces)) {
			case 1:
				$urlManagersInheritance[$name] = null;
				break;
			case 2:
				$urlManagersInheritance[$name] = trim($pieces[1]);
				break;
			default:
				throw new Exception("Illegal Ini file.");
		}
	
		$urlManagers[$name] = $urlManagerIni;
	}
	
	foreach($urlManagers as $urlManagerName => $urlManager) {
		$parent = $urlManagersInheritance[$urlManagerName];
		while($parent) {
			$urlManager = array_merge($urlManagers[$parent], $urlManager);
			$parent = $urlManagersInheritance[$parent];
		}
		$urlManagers[$urlManagerName] = $urlManager;
	}
	return $urlManagers;
}

function getTypes($urlManager) {
	$types = array();
	
	if(array_key_exists("params.hd_flash", $urlManager))
		$types[] = array(DeliveryProfileType::AKAMAI_HD, PlaybackProtocol::AKAMAI_HD); 
	
	if(array_key_exists("params.hd_ios", $urlManager) || array_key_exists("params.hd_secure_ios", $urlManager))
		$types[] = array(DeliveryProfileType::AKAMAI_HLS_MANIFEST, PlaybackProtocol::APPLE_HTTP);
	
	if(array_key_exists("params.rtsp_host", $urlManager))
		$types[] = array(DeliveryProfileType::AKAMAI_RTSP, PlaybackProtocol::RTSP);
	
	if(array_key_exists("params.hd_secure_hds", $urlManager))
		$types[] = array(DeliveryProfileType::AKAMAI_HDS, PlaybackProtocol::AKAMAI_HDS);
	
	if(array_key_exists("params.enforce_rtmpe", $urlManager))
		$types[] = array(DeliveryProfileType::AKAMAI_RTMP, PlaybackProtocol::RTMP);
	
	if(empty($types)) {
		print "Unsure format. Probably Akamai Http";
	}
	
	// Always have Http
	$types[] = array(DeliveryProfileType::AKAMAI_HTTP, PlaybackProtocol::HTTP);
	return $types;
}

function find($key, &$array) {
	if(!array_key_exists($key, $array))
		return null;
	$val = $array[$key];
	unset($array[$key]);
	return $val;
}

function createDelivery($key, $type, &$urlManager) {
	$deliveryType = $type[0];
	$streamerType = $type[1];
	$deliveryProfileClz = DeliveryProfilePeer::getClassByDeliveryProfileType($deliveryType);
	$deliveryProfile = new $deliveryProfileClz();
	
	$typename = preg_split('/(?=[A-Z])/',$deliveryProfileClz);
	$name = $key . " " . implode(" ", $typename);
	$deliveryProfile->setName($name);
	$deliveryProfile->setSystemName($name);
	$deliveryProfile->setDescription($name);
	$deliveryProfile->setType($deliveryType);
	$deliveryProfile->setStreamerType($streamerType);
	$deliveryProfile->setUrl($key);
	$deliveryProfile->setIsDefault(false);
	$deliveryProfile->setPartnerId(0);
	
	switch($deliveryType) {
		case DeliveryProfileType::AKAMAI_HTTP:
			$host = find("params.http_header_host", $urlManager);
			if($host) {
				$recognizer = new kUrlRecognizer();
				$recognizer->setHosts(explode(",", $host));
				$deliveryProfile->setRecognizer($recognizer);
			}
			$useIntelliseek = find("params.enable_intelliseek", $urlManager);
			if($useIntelliseek) 
				$deliveryProfile->setUseIntelliseek(true);
			
			break;
			
		case DeliveryProfileType::AKAMAI_RTSP:
			$host = find("params.rtsp_host", $urlManager);
			if($host)
				$deliveryProfile->setUrl($host);
			break;
		
		case DeliveryProfileType::AKAMAI_RTMP:
			$enforce = find("params.enforce_rtmpe", $urlManager);
			if($enforce)
				$deliveryProfile->setEnforceRtmpe($enforce);
			break;
			
		case DeliveryProfileType::AKAMAI_HD:
			$host = find("params.hd_flash", $urlManager);
			if($host)
				$deliveryProfile->setUrl($host);
			break;
			
		case DeliveryProfileType::AKAMAI_HDS:
			$host = find("params.hd_secure_hds", $urlManager);
			if($host)
				$deliveryProfile->setUrl($host);
			break;
		
		case DeliveryProfileType::AKAMAI_HLS_MANIFEST:
			$host = find("params.hd_ios", $urlManager);
			if($host)
				$deliveryProfile->setUrl($host);
			$host = find("params.hd_secure_ios", $urlManager);
			if($host)
				$deliveryProfile->setUrl($host);
			break;
	}
	
	return $deliveryProfile;
}

function handleUrlManager($key, $urlManager) {
	
	global $unsupportedToken, $dryRun;
	
	$clazz = find("class", $urlManager);
	if(!in_array($clazz, array("kAkamaiUrlManager", "kUrlManager"))) {
		print "*** Error: Can't transform $key\n";
		return;
	}
	
	print "Transforming Url Manager : $key\n";
	
	$types = getTypes($urlManager);	
	foreach($types as $type) {
		$delivery = createDelivery($key, $type, $urlManager);
		
		print "\t Saving delivery profile $key : $type[0]\n";
		if(!$dryRun)
			$delivery->save();	
	}
	
	$missingParams = array_diff_key($urlManager,array_flip($unsupportedToken));
	if(count($missingParams)) {
		print "Error: Not all parameters were handled. Url : $key \n";
		print_r(array_keys($missingParams));
		die;
	}
	
	$missingToken = array_intersect_key($urlManager, array_flip($unsupportedToken));
	if(count($missingToken)) {
		print "Warning: Tokenizer / Recognizer parameters were ignore. Please add them manually : " . implode(", ", array_keys($missingToken)) . "\n";
	}
	print "<--\n";
}

/**
 * Body
 * -------------- 
 */
$urlManagers = parseHierarchicalIniFile($urlManagersFilename);
foreach($urlManagers as $key => $urlManager) {
	handleUrlManager($key, $urlManager);
}

echo "Done.";

