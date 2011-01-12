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
	$cacheKey = "actions/$inputService/$inputAction";
}
else if ($inputService)
{
	$cacheKey = "services/$inputService";
}
else if ($inputObject)
{
	$cacheKey = "objects/$inputObject";
}
else
{
	$cacheKey = 'root';
}

$cacheLeftPaneFilePath = "$cachePath/leftpane.cache";
$cacheFilePath = "$cachePath/$cacheKey.cache";

// Html headers + scripts
require_once("header.php");

// display left pane
if (file_exists($cacheLeftPaneFilePath))
{
	print file_get_contents($cacheLeftPaneFilePath);
}
else 
{
	ob_start();
	
	require_once("left_pane.php");
		
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
