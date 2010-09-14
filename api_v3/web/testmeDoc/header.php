<?php 
	require_once("../../bootstrap.php"); 

	$serviceMap = KalturaServicesMap::getMap();
	$services = array_keys($serviceMap);
	
	$currentService = @$_GET["service"];
	$currentAction = @$_GET["action"];
	$currentObject = @$_GET["object"];
	
	$clientGenerator = new DummyForDocsClientGenerator();
	$list = array();
	$serviceMap = KalturaServicesMap::getMap();
	$services = array_keys($serviceMap);
	foreach($services as $index => $service)
	{
		$serviceReflector = new KalturaServiceReflector($service);
		if($serviceReflector->isDeprecated())
		{
			unset($services[$index]);
			continue;
		}
			
		$actions = $serviceReflector->getActions();
		foreach($actions as &$action) // we need only the keys
			$action = null;
		$list[$service] = $actions;
	}
	$clientGenerator->setIncludeList($list);
	$clientGenerator->load();
	$enums = $clientGenerator->getEnums();
	$stringEnums = $clientGenerator->getStringEnums();
	$arrays = $clientGenerator->getArrays();
	$filters = $clientGenerator->getFilters();
	$objects = $clientGenerator->getObjects();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Kaltura - TestMe Console Documentation</title>
	<link rel="stylesheet" type="text/css" href="css/main.css" />
	<script type="text/javascript" src="../testme/js/jquery-1.3.1.min.js"></script>
	<script type="text/javascript" src="../testme/js/jquery.scrollTo-min.js"></script>
	<!-- <script type="text/javascript" src="js/main.js"></script> -->
	<script type="text/javascript">
		$(function() {
			$(window).resize(function(){
				$(".left").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
				$(".right").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
			});
			$(window).resize();
			<?php if ($currentObject): ?>
			$(".left").scrollTo({top: 0, left: 0});
			$(".left").scrollTo($("#object_<?php echo $currentObject;?>"), 0, {axis:'y'});
			<?php endif; ?>
		});
	</script>
</head>
<body>
	<ul id="kmcSubMenu">
 	<li>
     <a href="../testme/index.php">Test Console</a>
    </li>
    <li class="active">
     <a href="#">API Documentation</a>
    </li>
   </ul>
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
				<?php foreach($services as $serviceId): ?>
					<?php 
						$serviceReflector = new KalturaServiceReflector($serviceId); 
						$actions = $serviceReflector->getActions();
					?>
					<li class="service<?php echo($currentService == $serviceId) ? " expended": ""?>">
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

	<div class="right">
		<div id="doc" >