<?php 
require_once("../../bootstrap.php");

$lib = $_GET['lib'];
header("Content-Disposition: attachment; filename=\"$lib\"");
$root = myContentStorage::getFSContentRootPath();
kFile::dumpFile("$root/content/generator/output/$lib");
