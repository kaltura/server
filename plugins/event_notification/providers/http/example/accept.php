<?php
$serverRoot = realpath(__DIR__ . '/../../../../../');

require_once($serverRoot . '/tests/lib/KalturaClient.php');
require_once($serverRoot . '/tests/lib/KalturaPlugins/KalturaHttpNotificationClientPlugin.php');

if(!isset($_GET['type']))
{
	$message = "Handler type not defined";
	header("HTTP/1.1 500 Internal Server Error: $message");
	die($message);
}

$log = $serverRoot . '/cache/accept.log';
file_put_contents($log, "

***********************************************************************
Handler type: " . $_GET['type'] . "\n", FILE_APPEND);

switch($_GET['type'])
{
	case 'object':
		$object = unserialize($_POST['data']);
		file_put_contents($log, print_r($object, true), FILE_APPEND);
		break;
		
	case 'json':
		file_put_contents($log, print_r($_POST['data'], true), FILE_APPEND);
		break;
		
	case 'xml':
		file_put_contents($log, print_r($_POST['data'], true), FILE_APPEND);
		break;
		
	case 'fields':
		file_put_contents($log, print_r($_POST, true), FILE_APPEND);
		break;
		
	case 'text':
		$rawPostData = file_get_contents("php://input");
		file_put_contents($log, $rawPostData, FILE_APPEND);
		break;
}

echo 'OK';