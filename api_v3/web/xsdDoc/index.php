<?php 
require_once("../../bootstrap.php");
$INPUT_PATTERN = "/^[a-zA-Z0-9_]*$/";
$SCHEME_PATTERN = "/^[a-zA-Z0-9_.]*$/";

ActKeyUtils::checkCurrent();
KalturaLog::setContext("XSD-DOC");

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
require_once("header.php");

if (file_exists($cacheFilePath))
{
	print file_get_contents($cacheFilePath);
	die;
}

ob_start();

require_once("left_pane.php");

?>
	<div class="right">
		<div id="doc" >
			<?php 
				if($inputPage)
					require_once("$inputPage.php");
				else if ($schemaType)
					require_once("schema_info.php"); 
			?>
		</div>
	</div>
<?php

$out = ob_get_contents();
ob_end_clean();
print $out;

kFile::setFileContent($cacheFilePath, $out);

require_once("footer.php");
