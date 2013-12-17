<?php

$serviceMap = KalturaServicesMap::getMap();
if (!array_key_exists(strtolower($service), $serviceMap))
{
    die('Service "'.$service.'" not found');
}

$serviceReflector = $serviceMap[strtolower($service)];

/* @var $serviceReflector KalturaServiceActionItem */
$actions = $serviceReflector->actionMap;
if (!array_key_exists($action, $actions))
{
	die('Action "'.$action.'" does not exist for service "'.$service.'"');
}
try
{
    $actionReflector = new KalturaActionReflector($service, $action, $actions[$action]);
	$actionParams = $actionReflector->getActionParams($action);
}
catch(Exception $ex)
{
	die('Action "'.$action.'" does not exist for service "'.$service.'"');
}
$actionInfo = $actionReflector->getActionInfo();

?>
<h2>Kaltura API</h2>
<table class="action">
	<tr>
		<th colspan="6" class="service_action_title"><?php echo $service; ?>:<?php echo $action; ?></th>
	</tr>
<?php 
$description = trim(nl2br($actionInfo->description));
if ($description):
?>
	<tr>
		<td  colspan="6" class="title">Description:</td>
	</tr>
	<tr>
		<td class="description" colspan="6"><?php echo $description; ?></td>
	</tr>
<?php 
endif;
if($actionParams): ?>
	<tr>
		<td colspan="6" class="title">Input Params</td>
	</tr>
	<tr>
		<th class="subtitle">Name</th>
		<th class="subtitle">Type</th>
		<th class="subtitle">Description</th>
		<th class="subtitle">Required</th>
		<th class="subtitle">Default Value</th>
		<th class="subtitle">Restrictions</th>
	</tr>
<?php
endif;
foreach($actionParams as $actionParam):
/*@var $actionParam KalturaParamInfo */
?>
	<tr>
		<td><?php echo  $actionParam->getName() ?></td>
		<td>
			<?php if ($actionParam->isComplexType()): ?>
				<a href="?object=<?php echo $actionParam->getType(); ?>"><?php echo $actionParam->getType();?></a>
			<?php else: ?>
				<?php echo $actionParam->getType(); ?>
			<?php endif; ?>
		</td>
		<td><?php echo $actionParam->getDescription(); ?></td>
		<td><?php echo ($actionParam->isOptional() ? '' : 'V'); ?></td>
		<td><?php echo ($actionParam->isEnum() || $actionParam->isStringEnum() ? $actionParam->getType() . '::' . $actionParam->getConstantName($actionParam->getDefaultValue()) : $actionParam->getDefaultValue()); ?></td>
		<td><?php 
			$constrains = array();
			$actionParamConstraints = $actionParam->getConstraints();
			foreach($actionParamConstraints as $constraintName => $constraintValue)
				$constrains[] = "$constraintName : $constraintValue";
			
			echo implode("<br/>", $constrains);
		?></td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td colspan="6" class="title">Output Type</td>
	</tr>
<?php
$returnValue = $actionReflector->getActionOutputType();
if ($returnValue):
?>
	<tr>
		<td colspan="6" ><a href="?object=<?php echo $returnValue->getType(); ?>"><?php echo $returnValue->getType();?></a></td>
	</tr>
<?php
else:
?>
	<tr>
		<td colspan="6" class="sub_title_no_output">No Output</td>
	</tr>
<?php
endif;
if (is_array($actionInfo->errors) && count($actionInfo->errors)):
?>
	<tr>
		<td colspan="6" class="title">Errors</td>
	</tr>
<?php
	foreach($actionInfo->errors as $error):
?>
	<tr>
		<td colspan="6"><?php echo  $error[1]; ?></td>
	</tr>
<?php
	endforeach;
endif;
?>
	<tr>
		<td colspan="6" class="title">Example HTTP Hit</td>
	</tr>
	<tr>
		<td colspan="6"><?php example_hit($service, $action, $actionParams); ?></td>
	</tr>
</table>
<?php

function doc_link($link_type, $target_obj, $complex_type)
{
	if (!$complex_type)
		return $target_obj;
	$base_link = "../docs/index.html";
	switch ( $link_type )
	{
		case 'kaltura_object':
			return "<a target=\"_blank\" href=\"$base_link?goto=$target_obj.html\" onclick=\"return kalturaInitModalBox('$base_link?goto=$target_obj.html');\">$target_obj</a>";
		case 'kaltura_service':
			return $target_obj;
	}
}

function example_hit( $service, $action , $actionParams )
{
	echo 'http://www.kaltura.com/api_v3/?service='.urlencode($service).'&action='.urlencode($action);
	echo '<br /><strong>POST fields:</strong><div class="post_fields">';
	$hit = '';
	foreach($actionParams as $actionParam)
	{
		if ($actionParam->isComplexType())
		{
			if ($actionParam->isEnum())
				$hit .= $actionParam->getName().'<br />';
			else // assume object
			{
				$props = $actionParam->getTypeReflector()->getProperties();
				foreach($props as $property)
				{
					$hit .= $actionParam->getName().':'.$property->getName().'<br />';
				}
			}
		}
		else
		{
			$hit .= $actionParam->getName().'<br />';
		}
	}
	echo $hit.'</div>';
}