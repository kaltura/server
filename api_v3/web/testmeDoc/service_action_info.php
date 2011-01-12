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
$actionInfo = $serviceReflector->getActionInfo($action);

?>
<h2>Kaltura API</h2>
<table class="action">
	<tr>
		<th colspan="3" class="service_action_title"><?php echo $service; ?>:<?php echo $action; ?></th>
	</tr>
	<tr>
		<td  colspan="3" class="title">Description:</td>
	</tr>
	<tr>
		<td class="description" colspan="3"><?php echo  nl2br($actionInfo->description); ?></td>
	</tr>
<?php if($actionParams): ?>
	<tr>
		<td colspan="3" class="title">Input Params</td>
	</tr>
	<tr>
		<th class="subtitle">name</th>
		<th class="subtitle">type</th>
		<th class="subtitle">Description</th>
	</tr>
<?php
endif;
foreach($actionParams as $actionParam):
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
		<td><?php echo  $actionParam->getDescription(); ?></td>
	</tr>
<?php endforeach;
$returnValue = $serviceReflector->getActionOutputType($action);
if ($returnValue):
?>
	<tr>
		<td colspan="3" class="title">Output Type</td>
	</tr>
	<tr>
		<th colspan="3" class="subtitle">type</th>
	</tr>
	<tr>
		<td colspan="3" ><a href="?object=<?php echo $returnValue->getType(); ?>"><?php echo $returnValue->getType();?></a></td>
	</tr>
<?php
else:
?>
	<tr>
		<td colspan="3" class="sub_title_no_output">No Output</td>
	</tr>
<?php
endif;
if (is_array($actionInfo->errors) && count($actionInfo->errors)):
?>
	<tr>
		<td colspan="3" class="title">Errors</td>
	</tr>
<?php
	foreach($actionInfo->errors as $error):
?>
	<tr>
		<td colspan="3"><?php echo  $error[1]; ?></td>
	</tr>
<?php
	endforeach;
endif;
?>
	<tr>
		<td colspan="3" class="title">Example HTTP Hit</td>
	</tr>
	<tr>
		<td colspan="3"><?php example_hit($service, $action, $actionParams); ?></td>
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
			if ($actionParam->isEnum() || $actionParam->isFile())
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