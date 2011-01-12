<?php 

function saveToCache($cacheFileName, $buffer)
{
	$cacheFileDir = pathinfo($cacheFileName, PATHINFO_DIRNAME);
	if (!file_exists($cacheFileDir) || !is_dir($cacheFileDir))
		mkdir($cacheFileDir, 0755, true);
	
	file_put_contents($cacheFileName, $buffer);
}

require_once("../../bootstrap.php"); 
require_once("helpers.php");

// get inputs
$inputPage = @$_GET["page"];
$inputService = @$_GET["service"];
$inputAction = @$_GET["action"];
$inputObject = @$_GET["object"];

// get cache file name
$cachePath = kConf::get("general_cache_dir").'/testmeDoc';

if ($inputPage)
{
	$cacheKey = $inputPage;
}
else if ($inputService && $inputAction)
{
	$cacheKey = "$inputService/$inputAction";
}
else if ($inputService)
{
	$cacheKey = $inputService;
}
else if ($inputObject)
{
	$cacheKey = $inputObject;
}
else
{
	$cacheKey = 'root';
}

$cacheLeftPaneFilePath = "$cachePath/leftpane.cache";
$cacheFilePath = "$cachePath/$cacheKey.cache";

// Html headers
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
			<?php if ($inputObject): ?>
			$(".left").scrollTo({top: 0, left: 0});
			$(".left").scrollTo($("#object_<?php echo $inputObject;?>"), 0, {axis:'y'});
			<?php endif; ?>
		});

		$(document).ready(function() {
			serviceListItems = $('li.service > a');
			for (i = 0; i < serviceListItems.length; i++)
			{
				if (serviceListItems[i].text.toLowerCase() == '<?php echo $inputService ?>')
				{
					serviceListItems[i].parentNode.className = 'service expended';
				}
			}
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

<?php 

// display left pane
if (file_exists($cacheLeftPaneFilePath))
{
	print file_get_contents($cacheLeftPaneFilePath);
}
else 
{
	ob_start();
	
	require_once("header.php");
		
	$out = ob_get_contents();
	ob_end_clean();
	print $out;
	
	saveToCache($cacheLeftPaneFilePath, $out);
}

// right pane - try to return from cache
if (file_exists($cacheFilePath))
{
	print file_get_contents($cacheFilePath);
	die;
}

// right pane - not already cached - rebuild
ob_start();

?>
	<div class="right">
		<div id="doc" >
<?php 

if ($inputPage)
{
	if (in_array($inputPage, array("inout", "notifications", "overview", "terminology", "multirequest")))
		require_once("static_doc/".$inputPage.".php");
	else
		die('Page "'.$inputPage.'" not found');
}
else if ($inputService && $inputAction)
{
	$service = $inputService;
	$action = $inputAction;
	require_once("service_action_info.php");
}
else if ($inputService)
{
	$service = $inputService;
	require_once("service_info.php");
}
else if ($inputObject)
{
	$object = $inputObject;
	require_once("object_info.php");
}

?>
		</div>
	</div>
<?php 

$out = ob_get_contents();
ob_end_clean();
print $out;

saveToCache($cacheFilePath, $out);

require_once("footer.php");
