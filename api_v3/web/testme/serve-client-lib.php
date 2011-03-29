<?php 
require_once("../../bootstrap.php");

$lib = $_GET['lib'];
header("Content-Disposition: attachment; filename=\"$lib\"");
kFile::dumpFile(realpath(dirname(__FILE__)) . '/../../../generator/output/' . $lib);