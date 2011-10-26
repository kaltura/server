<?php
function getClassHierarchy($className) {
	$classesHierarchy = array ();
	$curClass = $className;
	for(;;) {
		$classesHierarchy [] = $curClass;
		
		$curClass = get_parent_class ( $curClass );
		if (! $curClass) {
			break;
		}
	}
	
	return array_reverse ( $classesHierarchy );
}

function isObjectNameValid($object) {
	if (! class_exists ( $object )) {
		return FALSE;
	}
	
	// make sure type reflection is initialized only for api object that exists in lib/types directory
	$reflectionClass = new ReflectionClass ( $object );
	$classFileLocation = realpath ( $reflectionClass->getFileName () );
	$allowedDirectory = realpath ( KALTURA_API_PATH . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "types" );
	if (strpos ( $classFileLocation, $allowedDirectory ) !== 0) {
		// for plugins object that exists in plugins directory
		$allowedDirectory = realpath ( KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "plugins" );
		if (strpos ( $classFileLocation, $allowedDirectory ) !== 0) {
			return FALSE;
		}
	}
	return TRUE;
}

function comparePropNames($prop1, $prop2) {
	return strcmp ( $prop1->getName (), $prop2->getName () );
}

if (! isObjectNameValid ( $object )) {
	die ( 'Object "' . $object . '" not found' );
	return;
}

$typeReflector = KalturaTypeReflectorCacher::get ( $object );
$type = $typeReflector->getType ();
$description = trim ( formatDescription ( $typeReflector->getDescription () ) );
$plugin = $typeReflector->getPlugin();
$classHierarchy = getClassHierarchy($typeReflector->getType());
?>
<h2><img src="images/object.png" align="middle"/> <?php echo $type; ?></h2>
<?php 
	if($description || $plugin || $typeReflector->isArray())
	{
		?>
		<div class="box">
			<h3>Description</h3>
			<?php 
				if ($description)
					echo "<p>$description</p>";
					
				echo "<ul>";
			
				if ($plugin) 
					echo "<li>Plugin: $plugin</li>";
				
				if ($typeReflector->isArray()) 
				{
					$arrayType = $typeReflector->getArrayType();
					echo "<li>Array type: <a href=\"#\" onclick=\"KDoc.openObject('$arrayType')\">$arrayType</a></li>";
				}
					
				echo "</ul>";
			?>
		</div>
		<?php
	}
?>

<div class="box">
	<h3>Class Hierarchy</h3>
	<ul>
	<?php 
	
		$classProperties = array ();
		foreach ( $classHierarchy as $curClass ) 
		{
			$curReflector = KalturaTypeReflectorCacher::get ( $curClass );
			
			if ($typeReflector->isEnum () || $typeReflector->isStringEnum ()) {
				$properties = $curReflector->getConstants ();
			} else {
				$properties = $curReflector->getCurrentProperties ();
			}
			
			usort ( $properties, 'comparePropNames' );
			$classProperties [$curClass] = $properties;
			
			$classPath = array();
			if($curReflector->getPackage())
				$classPath[] = $curReflector->getPackage();
			if($curReflector->getSubpackage())
				$classPath[] = $curReflector->getSubpackage();
			$classPath[] = $curReflector->getType();
			$classPath = implode('.', $classPath);
				
			echo "<li><a href=\"#\" onclick=\"KDoc.openObject('" . $curReflector->getType() . "')\">$classPath</a><ul>";
		}
		echo str_repeat("</ul></li>", count($classHierarchy));
	?>
	</ul>
</div>

<?php 


KalturaTypeReflector::setClassInheritMapPath ( $cachePath . "/classInheritMap.cache" );
if (! KalturaTypeReflector::hasClassInheritMapCache ()) 
{
	$config = new Zend_Config_Ini ( "../../../config/testme.ini", null, array ('allowModifications' => true ) );
	$config = KalturaPluginManager::mergeConfigs ( $config, 'testme' );
	$indexConfig = $config->get ( 'testmedoc' );
	
	$include = $indexConfig->get ( "include" );
	$exclude = $indexConfig->get ( "exclude" );
	$additional = $indexConfig->get ( "additional" );
	
	$clientGenerator = new DummyForDocsClientGenerator ();
	$clientGenerator->setIncludeOrExcludeList ( $include, $exclude );
	$clientGenerator->setAdditionalList ( $additional );
	$clientGenerator->load ();
	
	$objects = $clientGenerator->getTypes ();
	
	KalturaTypeReflector::setClassMap ( array_keys ( $objects ) );
}

$directChildren = array ();
$indirectChildren = array ();
foreach ( KalturaTypeReflector::getSubClasses ( $type ) as $subClass ) 
{
	$link = "<a href=\"#\" onclick=\"KDoc.openObject('$subClass')\">$subClass</a>";
	if (get_parent_class ( $subClass ) == $type)
		$directChildren [] = $link;
	else
		$indirectChildren[] = $link;
}
sort ( $directChildren );
sort ( $indirectChildren );

if (count ( $directChildren ) || count ( $indirectChildren )) 
{				
	?>
		<div class="box">
			<h3>Sub classes</h3>
			<?php 
				if (count ( $directChildren )) 
				{
					?>
					<h4>
						<img class="direct-sub-classes-<?php echo $type; ?>" src="images/expended.gif" onclick="KDoc.toggleItem('direct-sub-classes-<?php echo $type; ?>')"/>
						<img class="direct-sub-classes-<?php echo $type; ?>" src="images/collapsed.gif" onclick="KDoc.toggleItem('direct-sub-classes-<?php echo $type; ?>')" style="display: none;"/> 
						Direct sub classes
					</h4>
					<ul class="direct-sub-classes-<?php echo $type; ?>">
					<?php
						foreach($directChildren as $link)
							echo "<li>$link</li>";
					?>
					</ul>
					<?php 
				}
				
				if (count ( $indirectChildren )) 
				{
					?>
					<h4>
						<img class="indirect-sub-classes-<?php echo $type; ?>" src="images/expended.gif" onclick="KDoc.toggleItem('indirect-sub-classes-<?php echo $type; ?>')" style="display: none;"/>
						<img class="indirect-sub-classes-<?php echo $type; ?>" src="images/collapsed.gif" onclick="KDoc.toggleItem('indirect-sub-classes-<?php echo $type; ?>')"/> 
						Indirect sub classes
					</h4>
					<ul class="indirect-sub-classes-<?php echo $type; ?>" style="display: none;">
					<?php
						foreach($indirectChildren as $link)
							echo "<li>$link</li>";
					?>
					</ul>
					<?php 
				}
			?>
		</div>
	<?php
}

if (!$typeReflector->isArray ()) 
{
	if ($typeReflector->isEnum () || $typeReflector->isStringEnum ())
	{	
		?>
		<div class="box">
			<h3>Enumerations</h3>
			<?php 
				$propertiesClasses = array();
				foreach($classProperties as $class => $properties)
				{
					if(!count($properties))
						continue;
						
					?>
					<h4>
						<img class="props-sum-<?php echo $class; ?>" src="images/expended.gif" onclick="KDoc.toggleItem('props-sum-<?php echo $class; ?>')"/>
						<img class="props-sum-<?php echo $class; ?>" src="images/collapsed.gif" onclick="KDoc.toggleItem('props-sum-<?php echo $class; ?>')" style="display: none;"/> 
						<?php echo $class; ?>
					</h4>
					<ul class="props-sum-<?php echo $class; ?>">
					<?php
						foreach($properties as $property)
						{
							/* @var $property KalturaPropertyInfo */
							$name = $property->getName();
							$propertiesClasses[$name] = $class;
							echo "<li>$name (" . $property->getDefaultValue() . ")";
							
							if($property->getDescription())
								echo "<p>" . $property->getDescription() . "</p>";
								
							echo "</li>";
						} 
					?>
					</ul>
					<?php 
				}
			?>
		</div>
		<?php
	}	
	else
	{		
		?>
		<div class="box">
			<h3>Properties Summary</h3>
			<?php 
				$propertiesClasses = array();
				foreach($classProperties as $class => $properties)
				{
					if(!count($properties))
						continue;
						
					?>
					<h4>
						<img class="props-sum-<?php echo $class; ?>" src="images/expended.gif" onclick="KDoc.toggleItem('props-sum-<?php echo $class; ?>')"/>
						<img class="props-sum-<?php echo $class; ?>" src="images/collapsed.gif" onclick="KDoc.toggleItem('props-sum-<?php echo $class; ?>')" style="display: none;"/> 
						<?php echo $class; ?>
					</h4>
					<ul class="props-sum-<?php echo $class; ?>">
					<?php
						foreach($properties as $property)
						{
							/* @var $property KalturaPropertyInfo */
							$name = $property->getName();
							$propertiesClasses[$name] = $class;
							echo "<li><a href=\"#$type-$name\">$name</a></li>";
						} 
					?>
					</ul>
					<?php 
				}
			?>
		</div>
		<div class="box">
			<h3>Properties details</h3>
			<?php 
				$properties = $typeReflector->getProperties();
				usort ( $properties, 'comparePropNames' );
				foreach($properties as $property)
				{
					/* @var $property KalturaPropertyInfo */
				
					$description = formatDescription($property->getDescription());
					if ($property->getName() == "orderBy")
						$description = "This parameter sets the order criteria by which objects will be retrieved. ";
					
					?>
					<h4><a name="<?php echo $type . '-' . $property->getName(); ?>"></a><?php echo $property->getName(); ?></h4>
					<?php 
						if($description)
							echo "<p>$description</p>";
					?>
					<ul>
						<?php 
				
							if($property->getName() == "orderBy")
							{
								$filterEnumOrder = str_replace("Filter", "OrderBy", $type);
								if (class_exists($filterEnumOrder))
									echo "<li>Enumerator type: <a href=\"#\" onclick=\"KDoc.openObject('$filterEnumOrder')\">$filterEnumOrder</a></li>";
								else
									echo "<li>Type: <a href=\"#\" onclick=\"KDoc.openObject('" . $property->getType() . "')\">" . $property->getType() . "</a></li>";
							}
							elseif($property->isArray())
							{
								if($property->getArrayType())
									echo "<li>Array of type: <a href=\"#\" onclick=\"KDoc.openObject('" . $property->getArrayType() . "')\">" . $property->getArrayType() . "</a></li>";
							}
							elseif($property->isEnum() || $property->isStringEnum())
							{
								echo "<li>Enumerator type: <a href=\"#\" onclick=\"KDoc.openObject('" . $property->getType() . "')\">" . $property->getType() . "</a></li>";
							}
							elseif($property->isFile() || $property->isSimpleType())
							{
								echo "<li>Type: " . $property->getType() . "</li>";
							}
							else 
							{
								echo "<li>Type: <a href=\"#\" onclick=\"KDoc.openObject('" . $property->getType() . "')\">" . $property->getType() . "</a></li>";
							}
							
							if(isset($propertiesClasses[$property->getName()]))
							{
								$class = $propertiesClasses[$property->getName()];
								echo "<li>Defined in: <a href=\"#\" onclick=\"KDoc.openObject('$class')\">$class</a></li>";
							}
							
							if($property->getDynamicType())
								echo "<li>Comma separated values of type: <a href=\"#\" onclick=\"KDoc.openObject('" . $property->getDynamicType() . "')\">" . $property->getDynamicType() . "</a></li>";
								
							if($property->isDeprecated())
							{
								$message = 'Deprecated';
								if($property->getDeprecationMessage())
									$message .= ' - ' . $property->getDeprecationMessage();
									
								echo "<li>$message</li>";
							}
								
							if(count($property->getPermissions()))
							{
								echo "<li>Required permissions<ul>";
								foreach($property->getPermissions() as $permission)
									echo "<li>" . ucfirst($permission) . "</li>";
								echo "</ul></li>";
							}
							
							if($property->isInsertOnly())
								echo "<li>Insert only</li>";
							if($property->isReadOnly())
								echo "<li>Read only</li>";
							if($property->isWriteOnly())
								echo "<li>Write only</li>";
						?>
					</ul>
					<?php 
				}
			?>
		</div>
		<?php
	}
}

