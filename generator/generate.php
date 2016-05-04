<?php 

$xmlGenerator = realpath(__DIR__ . '/../api_v3/generator/generate_xml.php');
passthru("php $xmlGenerator");

require_once(__DIR__ . "/exec.php");
