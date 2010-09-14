<?php require_once("header.php"); ?>
<?php require_once("helpers.php"); ?>
<?php 
$service = @$_GET["service"];
$action = @$_GET["action"];
$page = @$_GET["page"];
$object = @$_GET["object"];


if ($page)
{
	if (in_array($page, array("inout", "notifications", "overview", "terminology", "multirequest")))
		require_once("static_doc/".$page.".php");
}
else if ($service && $action)
{
	require_once("service_action_info.php");
}
else if ($service)
{
	require_once("service_info.php");
}
else if ($object)
{
	require_once("object_info.php");
}
?>
<?php require_once("footer.php"); ?>