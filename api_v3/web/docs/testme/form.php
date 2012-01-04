<?php 
	require_once("../../../bootstrap.php");
	ActKeyUtils::checkCurrent();
	KalturaLog::setContext("TESTME");
	
	$configSection = 'testme';
	if(isset($_REQUEST['config']))
		$configSection = $_REQUEST['config'];
		
	$config = new Zend_Config_Ini("../../../config/testme.ini", null, array('allowModifications' => true));
	$config = KalturaPluginManager::mergeConfigs($config, 'testme');
	$indexConfig = $config->get($configSection);
	
	$include = $indexConfig->get("include");
	$exclude = $indexConfig->get("exclude");
	$excludePaths = $indexConfig->get("excludepaths");

	$cacheFileName = kConf::get("cache_root_path").'/testme/services-'.$configSection;
	
	if (file_exists($cacheFileName))
	{
		$services = unserialize(file_get_contents($cacheFileName));
	}
	else
	{
		$clientGenerator = new DummyForDocsClientGenerator();
		$clientGenerator->setIncludeOrExcludeList($include, $exclude, $excludePaths);
		$clientGenerator->load();
		
		$services = $clientGenerator->getServices();
		kFile::setFileContent($cacheFileName, serialize($services));
	}
	
	$list = array();
	foreach($services as $serviceName => $serviceReflector)
	{
		if($serviceReflector->isServerOnly())
			unset($services[$serviceName]);
	}
	ksort($services, SORT_STRING);
	
?>
<div>
	<div class="param">
		<label for="history">History: </label>
		<select name="history"></select>
		<img id="actionHelp" src="images/help.png" class="help" title="After you send an API call, you can see the history of the API calls of the current session." />
	</div>

	<div class="param">
		<label for="ks">KS (string):</label>
		<input type="text" class="" name="ks" size="30" />
		<input id="chk-ks" type="checkbox" checked="checked" />
		<img id="actionHelp" src="images/help.png" class="help" title="Kaltura Session string." />
	</div>
		
	<?php if($indexConfig->noCache): ?>
	<div class="param">
		<label for="ks">No Cache (bool):</label>
		<input type="text" class="" name="nocache" size="30" />
		<input type="checkbox" />
	</div>
	<?php endif; ?>

	<div class="param">
		<label for="service">Select service:</label> 
		<select name="service">
			<?php 
				foreach($services as $serviceReflector)
				{
					/* @var $serviceReflector KalturaServiceReflector */
					$serviceId = $serviceReflector->getServiceId();
					$serviceName = $serviceReflector->getServiceName();
					$serviceLabel = $serviceReflector->getServiceName();
					$pluginName = $serviceReflector->getPluginName();
					
					if ($pluginName)
						$serviceName = "$pluginName.$serviceName";
					
					if ($serviceReflector->isDeprecated())
						$serviceLabel . ' (deprecated)';
					
					echo "<option value=\"$serviceId\" title=\"$serviceName\">$serviceLabel</option>";
				}
			?>
		</select>
		<img id="actionHelp" src="images/help.png" class="help" title="Select an API service to display. Click the arrow for the full list of services." />
	</div>
	
	<div class="param">
		<label for="action">Select action:</label>
		<select name="action">
			<option>Select a service...</option>
		</select>
		<img id="actionHelp" src="images/help.png" class="help" title="Select one of the service's available actions. Click the arrow for the full list of actions." />
	</div>

	<div>
		<div id="action-params"></div>
		<div id="objects-containter"></div>
	</div>
	
	<div>
		<button id="send" type="button" title="Send an API call.">Send</button>
	</div>
		
	<input type="hidden" name="format" value="<?php echo KalturaResponseType::RESPONSE_TYPE_JSON; ?>"/>
	<input type="hidden" name="content-type" value="text/html"/>
	
	<form id="form" action="" method="post"></form>
	
	<?php 
		if (kConf::hasParam("testme_tracking_code"))
		{
			require(kConf::get("testme_tracking_code"));
		}
	?>
</div>
