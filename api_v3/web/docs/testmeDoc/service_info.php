<?php

try 
{
	$serviceReflector = new KalturaServiceReflector ( $service );
} 
catch ( Exception $ex ) 
{
	die ( 'Service "' . $service . '" not found' );
}
$actions = array_keys ( $serviceReflector->getActions () );
$serviceInfo = $serviceReflector->getServiceInfo ();
$description = nl2br ( $serviceInfo->description );
?>
<h2><img src="images/service.png" align="middle"/> <?php echo $serviceInfo->serviceName; ?></h2>
<div class="box">
	<h3>Description</h3>
	<?php 
		if ($description)
			echo "<p>$description</p>";
			
		echo "<ul>";
		$classPath = array();
		if($serviceInfo->package)
			$classPath[] = $serviceInfo->package;
		if($serviceInfo->subpackage)
			$classPath[] = $serviceInfo->subpackage;
		$classPath[] = $serviceReflector->getServiceClass();
		$classPath = implode('.', $classPath);
		echo "<li>Class: $classPath</li>";
	
		$plugin = $serviceReflector->getPluginName();
		if ($plugin) 
			echo "<li>Plugin: $plugin</li>";
		
		if ($serviceReflector->isDeprecated()) 
		{
			$message = 'Deprecated';
			if($serviceReflector->getDeprecationMessage())
				$message .= ' - ' . $serviceReflector->getDeprecationMessage();
				
			echo "<li>$message</li>";
		}
			
		if ($serviceInfo->abstract) 
			echo "<li>Abstract</li>";
			
		echo "</ul>";
	
?>
</div>

<div class="box">
	<h3>Actions</h3>
	<?php 
		echo "<ul>";
		
		foreach ( $actions as $action ) 
		{
			$actionInfo = $serviceReflector->getActionInfo ( $action );
			$serviceId = $serviceReflector->getServiceId ();
			$serviceName = $serviceReflector->getServiceName ();
			$actionName = $actionInfo->action;
			if($actionInfo->deprecated)
				$actionName .= " (deprecated)";
			$link = "<a href=\"#\" onclick=\"KDoc.openAction('$serviceId', '$serviceName', '$action')\">$actionName</a>";
			$description = nl2br($actionInfo->description);
			
			echo "<li>$link";
			if($description)
				echo "<p>$description</p>";
			echo "</li>";
		}
			
		echo "</ul>";
	
?>
</div>
