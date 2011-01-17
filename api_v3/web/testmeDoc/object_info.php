<?php
function getClassHierarchy($className)
{
	$classesHierarchy = array();
	$curClass = $className;
	for (;;)
	{
		$classesHierarchy[] = $curClass;
		
		$curClass = get_parent_class($curClass);
		if (!$curClass)
		{
			break;
		}
	}
	
	return array_reverse($classesHierarchy);
}

function isObjectNameValid($object)
{
	if (!class_exists($object))
	{
		return FALSE;
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
			return FALSE;
		}
	}
	return TRUE;
}

function comparePropNames($prop1, $prop2)
{
	return strcmp($prop1->getName(), $prop2->getName());
}

if (!isObjectNameValid($object))
{
	die('Object "'.$object.'" not found');
	return;
}

?>
 <script type="text/javascript">

 $(document).ready(function() {
	 // set the onclick handler of all collapsed/expended images to show / hide the table
	 $("tr > td > b > img").click(
			 function() {
				 var classHeaderTable = $(this).parents('table:first');
				 var propertiesTable = classHeaderTable.parent().find('table:last');
				 if ($(this).attr('src').indexOf('collapsed.gif') >= 0) {
					 $(this).attr('src', 'images/expended.gif');
					 propertiesTable.show();
					 }
				 else {
					 $(this).attr('src', 'images/collapsed.gif');
					 propertiesTable.hide();
					 }
				 });
	 });

</script>

<?php 
	
$typeReflector = KalturaTypeReflectorCacher::get($object);

$type = $typeReflector->getType();
$description = formatDescription($typeReflector->getDescription());
echo 
	"<h2>Kaltura API</h2>
		<table class=\"action\" width=\"80%\">
		<tr>
			<th colspan=\"4\" class=\"service_action_title\">$type</th>
		</tr>
		<tr>
			<td colspan=\"4\" class=\"title\">Description:</td>
		</tr>
		<tr>
			<td class=\"description\" colspan=\"3\">$description</td>
		</tr>";

if ($typeReflector->isArray())
{
	$arrayType = $typeReflector->getArrayType();
	echo 
		"<tr>
			<td colspan=\"4\">Array of type <a href=\"?object=$arrayType\">$arrayType</a></td>
		</tr>";
}
else
{
	// sub title
	if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
	{
		$subTitle = 'Enumerations';
	}
	else 
	{
		$subTitle = 'Properties';
	}
		
	echo 
		"<tr>
			<td colspan=\"4\" class=\"title\">
				$subTitle
			</td>
		</tr>";
	
	// property columns
	$columns = Array('Name', 'Type');
	if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
	{
		$columns[] = 'Value';
	}
	elseif (!$typeReflector->getInstance() instanceof KalturaFilter)
	{
		$columns[] = 'Writable';
	}
	$columns[] = 'Description';
	
	// build baseClass->properties mapping
	$classProperties = Array();
	foreach (getClassHierarchy($typeReflector->getType()) as $curClass)
	{
		$curReflector = KalturaTypeReflectorCacher::get($curClass);
		
		if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
		{
			$properties = $curReflector->getConstants();
		}
		else
		{
			$properties = $curReflector->getCurrentProperties();
		}
		
		if (count($properties) == 0)
		{
			continue;
		}
		
		usort($properties, 'comparePropNames');
		
		$classProperties[$curClass] = $properties;
	}
	
	$printClassHeaders = (count($classProperties) > 1);
	
	foreach ($classProperties as $curClass => $properties)
	{
		echo 
			"<tr>
				<td colspan=\"3\">";
			
		// print class header		
		$propTableStyle = '';
		if ($printClassHeaders)
		{
			if ($curClass == $object)
			{
				$classTitle = $curClass;
			}
			else 
			{
				$classTitle = "<img src=\"images/collapsed.gif\"> ";
				$classTitle .= "Inherited from <a href=\"?object=$curClass\">$curClass</a>";
				$propTableStyle = 'display:none';
			}

			echo 
				"<table style=\"\">
					<tbody>
						<tr>
							<td class=\"inheritedFrom\" colspan=\"4\"><b>
								$classTitle
							</b></td>
						</tr>
				</table>";
		}

		echo "<table style=\"$propTableStyle\"><tbody>";
		
		// print column headers
		echo "<tr>";
		foreach ($columns as $column)
		{
			echo "<th class=\"subtitle\">$column</th>";
		}
		echo "</tr>";

		// print properties
		$odd = true;
		foreach($properties as $property)
		{
			// start a new property row
			$odd = !$odd;			
			$rowProps = ($odd) ? " class=\"odd\"" : "";
			echo "<tr$rowProps>";
			
			// property name
			$propName = $property->getName();
			echo "<td>$propName</td>";
			
			// property type
			$propType = $property->getType();
			if ($property->isComplexType())
			{
				$propType = "<a href=\"?object=$propType\">$propType</a>";
			}
			echo "<td>$propType</td>";
		
			// property value
			if (in_array('Value', $columns))
			{
				$defaultVal = $property->getDefaultValue();
				echo "<td>$defaultVal</td>";
			}
			
			// property writable
			if (in_array('Writable', $columns))
			{
				$isWritable = $property->isReadOnly() ? '' : 'V';
				echo "<td>$isWritable</td>";
			}
			 
			// property description
			if ($property->getName() == "orderBy")
			{
				/* hack for filters order by */
				$filterEnumOrder = str_replace("Filter", "OrderBy", $object);
				$description =  
				"This parameter sets the order criteria by which objects will be retrieved. ";
				if (class_exists($filterEnumOrder))
				{
					$description .= "This parameter should by set according to the following enumeration: 
						<a href=\"?object=$filterEnumOrder\">$filterEnumOrder</a>.";
				}
			}
			else
			{
				$description = formatDescription($property->getDescription());
			}
			echo "<td>$description</td>";
			
			// end a property row
			echo "</tr>";
		}
		
		echo '</table></td></tr>';
	}
}
echo "</table>";
