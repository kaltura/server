<?php 
error_reporting(E_ALL);
ini_set( "memory_limit","512M" );

$xmlGenerator = realpath(__DIR__ . '/../api_v3/generator/generate_xml.php');
passthru("php $xmlGenerator");

require_once(__DIR__ . "/../../clients-generator/exec.php");