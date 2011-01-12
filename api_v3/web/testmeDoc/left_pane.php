<?php 

	$config = new Zend_Config_Ini("../../config/testme.ini");
	$indexConfig = $config->get('testmedoc');
	
	$include = $indexConfig->get("include");
	$exclude = $indexConfig->get("exclude");
	$additional = $indexConfig->get("additional");
		
	$clientGenerator = new DummyForDocsClientGenerator();
	$clientGenerator->setIncludeOrExcludeList($include, $exclude);
	$clientGenerator->setAdditionalList($additional);
	$clientGenerator->load();
	
	$list = array();
	$services = $clientGenerator->getServices();
	foreach($services as $serviceName => $serviceReflector)
	{
		if($serviceReflector->isDeprecated())
		{
			unset($services[$serviceName]);
			continue;
		}
			
		$actions = $serviceReflector->getActions();
		foreach($actions as &$action) // we need only the keys
			$action = null;
		$list[$serviceName] = $actions;
	}
	$clientGenerator->setIncludeList($list);
	$enums = $clientGenerator->getEnums();
	$stringEnums = $clientGenerator->getStringEnums();
	$arrays = $clientGenerator->getArrays();
	$filters = $clientGenerator->getFilters();
	$objects = $clientGenerator->getObjects();

?>
	<div class="left">
		<div class="left-content">
			<div id="general">
				<h2>General</h2>
				<ul>
					<li><a href="?page=overview">Overview</a></li>
					<li><a href="?page=terminology">Terminology</a></li>
					<li><a href="?page=inout">Request/Response structure</a></li>
					<li><a href="?page=multirequest">multiRequest</a></li>
					<li><a href="?page=notifications">Notifications</a></li>
				</ul>
			</div>

			<div id="services">
				<h2>Services</h2>
				<ul class="services">
				<?php foreach($services as $serviceReflector): ?>
					<?php 
						$serviceId = $serviceReflector->getServiceId();
						$actions = $serviceReflector->getActions();
					?>
					<li class="service">
						<a href="?service=<?php echo $serviceId; ?>"><?php echo $serviceReflector->getServiceName(); ?></a>
						<ul class="actions">
						<?php foreach($actions as $actionId => $actionName): ?>
							<li class="action"><a href="?service=<?php echo $serviceId; ?>&action=<?php echo $actionId; ?>"><?php echo $actionName;?></a></li>
						<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="objects">
				<h2>General Objects</h2>
				<ul>
				<?php foreach($objects as $object): ?>
					<li id="object_<?php echo $object->getType(); ?>">
						<a href="?object=<?php echo $object->getType(); ?>"><?php echo $object->getType(); ?></a>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="objects">
				<h2>Filter Objects</h2>
				<ul>
				<?php foreach($filters as $object): ?>
					<li id="object_<?php echo $object->getType(); ?>">
						<a href="?object=<?php echo $object->getType(); ?>"><?php echo $object->getType(); ?></a>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="objects">
				<h2>Array Objects</h2>
				<ul>
				<?php foreach($arrays as $object): ?>
					<li id="object_<?php echo $object->getType(); ?>">
						<a href="?object=<?php echo $object->getType(); ?>"><?php echo $object->getType(); ?></a>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="enums">
				<h2>Enums</h2>
				<ul>
				<?php foreach($enums as $enum): ?>
					<li id="object_<?php echo $enum->getType(); ?>">
						<a href="?object=<?php echo $enum->getType(); ?>" name="<?php echo $enum->getType(); ?>"><?php echo $enum->getType(); ?></a>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="enums">
				<h2>String Enums Constants</h2>
				<ul>
				<?php foreach($stringEnums as $stringEnum): ?>
					<li id="object_<?php echo $stringEnum->getType(); ?>">
						<a href="?object=<?php echo $stringEnum->getType(); ?>" name="<?php echo $stringEnum->getType(); ?>"><?php echo $stringEnum->getType(); ?></a>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
