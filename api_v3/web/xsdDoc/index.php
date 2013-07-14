<?php 

require_once(dirname(__FILE__) . '/../../../alpha/config/kConf.php');

$INPUT_PATTERN = "/^[a-zA-Z0-9_]*$/";
$SCHEME_PATTERN = "/^[a-zA-Z0-9_.]*$/";

// get inputs
$inputPage = @$_GET["page"];
$schemaType = @$_GET["type"];

if ((preg_match ($INPUT_PATTERN, $inputPage) !== 1) || (preg_match ($SCHEME_PATTERN, $schemaType) !== 1)) {
	print "Illegal input. Page & schemaType must be alpha-numeric";
	die;
}

// get cache file name
$cachePath = kConf::get("cache_root_path").'/xsdDoc';
$cacheKey = 'root';
if($inputPage)
	$cacheKey = $inputPage;
elseif($schemaType)
	$cacheKey = $schemaType;

$cacheFilePath = "$cachePath/$cacheKey.cache";

// Html headers + scripts
if (file_exists($cacheFilePath))
{
	require_once(__DIR__ . "/header.php");
	print file_get_contents($cacheFilePath);
	die;
}

require_once(__DIR__ . "/../../bootstrap.php");

ActKeyUtils::checkCurrent();
KalturaLog::setContext("XSD-DOC");

KalturaLog::debug(">------------------------------------- xsd doc -------------------------------------");

require_once(__DIR__ . "/header.php");

ob_start();

require_once(__DIR__ . "/left_pane.php");

?>
	<div class="right">
		<div id="doc" >
			<?php 
				if($inputPage)
					require_once(__DIR__ . "/$inputPage.php");
				else if ($schemaType)
					require_once(__DIR__ . "/schema_info.php"); 
			?>
		</div>
	</div>
<?php

$out = ob_get_contents();
ob_end_clean();
print $out;

kFile::setFileContent($cacheFilePath, $out);

require_once(__DIR__ . "/footer.php");

KalturaLog::debug("<------------------------------------- xsd doc -------------------------------------");
