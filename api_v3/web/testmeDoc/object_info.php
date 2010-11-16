<?php
if (!class_exists($object))
{
	echo "Object not found";
	return;
}

// make sure type reflection is initialized only for api object that exists in lib/types directory
$reflectionClass = new ReflectionClass($object);
$classFileLocation = realpath($reflectionClass->getFileName());
$allowedDirectory = realpath(KALTURA_API_PATH . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "types");
if (strpos($classFileLocation, $allowedDirectory) !== 0)
{
	// for plugins object that exists in plugins directory
	$allowedDirectory = realpath(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "plugins");
	if (strpos($classFileLocation, $allowedDirectory) !== 0)
	{
		echo "Object not found";
		return;
	}
}
	
$typeReflector = KalturaTypeReflectorCacher::get($object);
if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
	$properties = $typeReflector->getConstants();
else
	$properties = $typeReflector->getProperties();
?>
<h2>Kaltura API</h2>
<table class="action" width="80%">
	<tr>
		<th colspan="4" class="service_action_title"><?php echo $typeReflector->getType(); ?></th>
	</tr>
	<tr>
		<td colspan="4" class="title">Description:</td>
	</tr>
	<tr>
		<td class="description" colspan="3"><?php echo formatDescription($typeReflector->getDescription()); ?></td>
	</tr>
	<?php if ($typeReflector->isArray()): ?>
	<tr>
		<td colspan="4">Array of type <a href="?object=<?php echo $typeReflector->getArrayType(); ?>"><?php echo $typeReflector->getArrayType(); ?></a></td>
	</tr>
	<?php else: ?>
	<tr>
		<td colspan="4" class="title">
			<?php if ($typeReflector->isEnum() || $typeReflector->isStringEnum()): ?>
				Enumerations
			<?php else: ?>
				Properties
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th class="subtitle">Name</th>
		<th class="subtitle">Type</th>
		<?php if ($typeReflector->isEnum() || $typeReflector->isStringEnum()): ?>
		<th class="subtitle">Value</th>
		<?php endif; ?>
		<th class="subtitle">Description</th>
	</tr>
	<?php $odd = true; ?>
	<?php foreach($properties as $property): ?>
	<?php $odd = !$odd;?>
	<tr<?php echo ($odd) ? " class=\"odd\"" : ""; ?>>
		<td><?php echo $property->getName(); ?></td>
		<td>
			<?php if ($property->isComplexType()): ?>
				<a href="?object=<?php echo $property->getType(); ?>"><?php echo $property->getType();?></a>
			<?php else: ?>
				<?php echo $property->getType(); ?>
			<?php endif; ?>
		</td>
		<?php if ($typeReflector->isEnum() || $typeReflector->isStringEnum()): ?>
		<td>
			<?php echo $property->getDefaultValue(); ?>
		</td>
		<?php endif; ?>
		<td>
			<?php if ($property->getName() == "orderBy"): /* hack for filters order by */ ?>
				<?php $filterEnumOrder = str_replace("Filter", "OrderBy", $object); ?>
				This parameter sets the order criteria by which objects will be retrieved. This parameter should by set according to the following enumeration: <a href="?object=<?php echo $filterEnumOrder; ?>"><?php echo $filterEnumOrder; ?></a>.
			<?php else: ?>
				
				<?php echo formatDescription($property->getDescription()); ?>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php 
		if ($typeReflector->isDynamicEnum())
		{
			$type = $typeReflector->getType();
			// TODO remove call_user_func after moving to php 5.3
			$baseEnumName = call_user_func("$type::getEnumClass");
//			$baseEnumName = $type::getEnumClass();
			$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
			foreach($pluginInstances as $pluginInstance)
			{
				$enums = $pluginInstance->getEnums($baseEnumName);
				foreach($enums as $enum)
				{
					// TODO remove call_user_func after moving to php 5.3
					$enumConstans = call_user_func("$enum::getAdditionalValues");
//					$enumConstans = $enum::getAdditionalValues();
					foreach($enumConstans as $name => $value)
					{
						$odd = !$odd;
						
						// TODO remove call_user_func after moving to php 5.3
						$value = call_user_func("$enum::get")->apiValue($value);
//						$value = $enum::get()->apiValue($value);
						
						?>
							<tr<?php echo ($odd) ? " class=\"odd\"" : ""; ?>>
								<td><?php echo $name; ?></td>
								<td>string</td>
								<td><?php echo $value; ?></td>
								<td></td>
							</tr>
						<?php
					}
				}
			}
		}
	?>
	<?php endif; ?>
</table>