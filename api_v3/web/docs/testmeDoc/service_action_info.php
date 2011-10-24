<?php
try
{
	$serviceReflector = new KalturaServiceReflector($service);
}
catch(Exception $ex)
{
	die('Service "'.$service.'" not found');
}
$actions = $serviceReflector->getActions();
try
{
	$actionParams = $serviceReflector->getActionParams($action);
}
catch(Exception $ex)
{
	die('Action "'.$action.'" does not exist for service "'.$service.'"');
}
$serviceInfo = $serviceReflector->getServiceInfo ();
$actionInfo = $serviceReflector->getActionInfo($action);
$description = trim(nl2br($actionInfo->description));
?>
<h2><img src="images/action.png" align="middle"/> <?php echo $service; ?>:<?php echo $action; ?></h2>
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
		
		if ($actionInfo->deprecated) 
		{
			$message = 'Deprecated';
			if($actionInfo->deprecationMessage)
				$message .= ' - ' . $actionInfo->deprecationMessage;
				
			echo "<li>$message</li>";
		}
			
		if ($actionInfo->abstract) 
			echo "<li>Abstract</li>";
			
		$returnValue = $serviceReflector->getActionOutputType($action);
		if ($returnValue)
			echo "<li>Output type: <a href=\"#\" onclick=\"KDoc.openObject('" . $returnValue->getType() . "')\">" . $returnValue->getType() . "</a></li>";
		else
			echo "<li>No Output</li>";
			
		echo "</ul>";
	?>
</div>

<?php 
	if($actionParams)
	{
		?>
		<div class="box">
			<h3>Input Parameters</h3>
			<?php 
				echo "<ul>";
				
				foreach($actionParams as $actionParam)
				{
					/* @var $actionParam KalturaParamInfo */
					echo "<li>" . $actionParam->getName();
					echo "<p>" . $actionParam->getDescription() . "</p><ul>";
					
					if ($actionParam->isComplexType())
						echo "<li>Type: <a href=\"#\" onclick=\"KDoc.openObject('" . $actionParam->getType() . "')\">" . $actionParam->getType() . "</a></li>";
					else
						echo "<li>Type: " . $actionParam->getType() . "</li>";
					echo "</ul></li>";
				}
					
				echo "</ul>";
			?>
		</div>
		<?php
	}
?>

<?php 
	if(count($actionInfo->errors))
	{
		?>
		<div class="box">
			<h3>Errors</h3>
			<?php 
				echo "<ul>";
				
				foreach($actionInfo->errors as $error)
					echo "<li>" . $error[1] . "</li>";
					
				echo "</ul>";
			?>
		</div>
		<?php
	}
?>

<div class="box">
	<h3>Example HTTP hit</h3>
	<p><?php example_hit($service, $action, $actionParams); ?></p>
</div>

<?php

function example_hit( $service, $action , $actionParams )
{
	echo 'http://www.kaltura.com/api_v3/?service='.urlencode($service).'&action='.urlencode($action);
	echo '<h4>POST fields:</h4><ul>';
	foreach($actionParams as $actionParam)
	{
		if ($actionParam->isComplexType())
		{
			if ($actionParam->isEnum())
				echo '<li>' . $actionParam->getName() . '</li>';
			else // assume object
			{
				$props = $actionParam->getTypeReflector()->getProperties();
				foreach($props as $property)
				{
					echo '<li>' . $actionParam->getName() . ':' . $property->getName() . '</li>';
				}
			}
		}
		else
		{
			echo '<li>' . $actionParam->getName() . '</li>';
		}
	}
	echo '</ul>';
}