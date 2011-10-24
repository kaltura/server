<?php 
require_once("../../../bootstrap.php"); 

// get inputs
$inputPage = @$_GET["page"];
$schemaType = @$_GET["type"];

// get cache file name
$cachePath = kConf::get("cache_root_path").'/xsdDoc';
$cacheKey = null;
if($inputPage)
	$cacheKey = $inputPage;
elseif($schemaType)
	$cacheKey = $schemaType;
else
	die;
	
$cacheFilePath = "$cachePath/$cacheKey.cache";
if (file_exists($cacheFilePath))
{
	print file_get_contents($cacheFilePath);
	die;
}	

ob_start();

	if($inputPage)
	{
		require_once("static_doc/$inputPage.php");
	}
	else if ($schemaType)
	{
		?>
		<div align="center">
		<div class="api-info">
		<?php 
				
		$schemaName = KalturaSchemaType::getDescription($schemaType);
		$downloadUrl = 'http://' . kConf::get('www_host') . "/api_v3/index.php/service/schema/action/serve/type/$schemaType/name/$schemaType.xsd";
		
		$schemaPath = SchemaService::getSchemaPath($schemaType);
		$xslPath = dirname(__FILE__) . '/xsl/type.xsl';
		
		// Load the XML source
		$xml = new DOMDocument;
		$xml->load($schemaPath);
		
		echo '<h2><img src="images/object.png" align="middle"/> ' . $schemaName . '</h2>';
		echo '<ul>';
		echo "<li>Download URL: <a href=\"$downloadUrl;\" target=\"_blank\">$downloadUrl</a></li>";
		if($xml->firstChild->hasAttribute('version'))
			echo "<li>Version: " . $xml->firstChild->getAttribute('version') . "</li>";
		echo '<ul>';
				
		$xsl = new DOMDocument;
		$xsl->load($xslPath);
		
		// Configure the transformer
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // attach the xsl rules
		
		echo $proc->transformToXML($xml);
		
		?>
		</div></div>
		<?php 
	}
	
$out = ob_get_contents();
ob_end_clean();
print $out;

//kFile::setFileContent($cacheFilePath, $out);
	
