<?php 
error_reporting(E_ALL);

require_once("../../bootstrap.php");

// get inputs
$schemaType = @$_GET["type"];

// get cache file name
$cachePath = kConf::get("cache_root_path").'/xsdDoc';
$cacheKey = 'root';
if ($schemaType)
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
			
				if ($schemaType)
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
