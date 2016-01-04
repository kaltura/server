<?php
require_once(__DIR__ . "/../../bootstrap.php");
ActKeyUtils::checkCurrent();
KalturaLog::setContext("TESTME");

$configSection = 'testme';
if(isset($_REQUEST['config']))
	$configSection = $_REQUEST['config'];

$config = new Zend_Config_Ini("../../config/testme.ini", null, array('allowModifications' => true));
$config = KalturaPluginManager::mergeConfigs($config, 'testme');
$indexConfig = $config->get($configSection);

$include = $indexConfig->get("include");
$exclude = $indexConfig->get("exclude");
$excludePaths = $indexConfig->get("excludepaths");

$cacheFileName = kConf::get("cache_root_path").'/testme/services-'.$configSection;

if (file_exists($cacheFileName))
{
	$services = unserialize(file_get_contents($cacheFileName));
}
else
{
	$clientGenerator = new DummyForDocsClientGenerator();
	$clientGenerator->setIncludeOrExcludeList($include, $exclude, $excludePaths);
	$clientGenerator->load();

	$serviceItems = $clientGenerator->getServices();

	$services = array();
	foreach($serviceItems as $serviceId => $serviceActionItem)
		$services[$serviceId] = $serviceActionItem;

	kFile::setFileContent($cacheFileName, serialize($services));
}

foreach($services as $serviceName => $serviceActionItem)
{
	/* @var $serviceActionItem KalturaServiceActionItem */
	if($serviceActionItem->serviceInfo->serverOnly)
		unset($services[$serviceName]);
}

function compareServicesByName( $srvA, $srvB )
{
	return strcasecmp( $srvA->serviceInfo->serviceName, $srvB->serviceInfo->serviceName );
}

usort($services, "compareServicesByName");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Kaltura - Test Me Console</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/main.css" />
	<link rel="stylesheet" type="text/css" href="css/code.example.css" />
	<link rel="stylesheet" type="text/css" href="css/http.spy.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.tooltip.css" />
	<script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.tooltip.js"></script>
	<script type="text/javascript" src="js/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" src="js/kCodeExample.js"></script>
	<script type="text/javascript" src="js/kField.js"></script>
	<script type="text/javascript" src="js/kDialog.js"></script>
	<script type="text/javascript" src="js/kTestMe.js"></script>
	<script type="text/javascript" src="js/ace-builds/src/ace.js"></script>
	<script type="text/javascript" src="js/kPrettify.js"></script>
	<!-- script type="text/javascript" src="js/kHttpSpy.js"></script -->
	<script type="text/javascript">
		<?php
            foreach($services as $serviceId => $serviceActionItem)
            {
                /* @var $serviceActionItem KalturaServiceActionItem */
                $serviceId = $serviceActionItem->serviceId;
                $serviceName = $serviceActionItem->serviceInfo->serviceName;
                $pluginName = $serviceActionItem->serviceInfo->package;
                $deprecated = ($serviceActionItem->serviceInfo->deprecated ? 'true' : 'false');

                echo "kTestMe.registerService(\"$serviceId\", \"$serviceName\", \"$pluginName\", $deprecated);\n";
            }
        ?>
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			var myframe = document.getElementById('hiddenResponse');

			if (myframe.attachEvent) {
				myframe.attachEvent('onload', function(){handleResponse();});

			} else {
				myframe.onload = function(){handleResponse();};
			}
		});


		function handleResponse(){
			var myframe = document.getElementById('hiddenResponse');
			var doc = ( myframe.contentDocument || myframe.contentWindow.document);

			var formatSelector = document.getElementsByName('format');
			var format = 'XML';
			if (formatSelector && formatSelector.length > 0){
				format = formatSelector[0].options[formatSelector[0].selectedIndex].text;
			}
			var text ="";
			if ( format == 'JSON' &&
				typeof doc.body != 'undefined' &&
				typeof doc.body.innerText != 'undefined'&&
				(doc.body.innerText.indexOf("{" != -1) )){
				format = 'json';
				text = indentJSON(doc.body.innerText, "\n", " ");
			} else if (format == 'XML' &&
				typeof doc.firstChild != 'undefined' )  {
				if ( doc.firstChild.localName == 'xml'){
					var para = document.createElement("div");
					para.appendChild(doc.firstChild);
					text = para.innerHTML;
					text = indentXML(text);
					format = 'xml';
				} else {
					var data = doc.getElementsByTagName("pre")[0];
					text = data.innerHTML;
				}
			} else {
				text = doc;
				format = 'txt';
			}
			document.getElementById("response").contentWindow.setAceEditorWithText(text, format);
			kTestMe.onResponse(text, format);
		}

	</script>
</head>
<?php

if(!isset($_REQUEST['hideMenu']) || !$_REQUEST['hideMenu'])
{
?>
<body class="body-bg">
<ul id="kmcSubMenu">
	<li class="active"><a href="#">Test Console</a></li>
	<li><a href="../testmeDoc/index.php">API Documentation</a></li>
	<li><a href="../xsdDoc/index.php">XML Schema</a></li>
	<li><a href="client-libs.php">API Client Libraries</a></li>
</ul>
<?php
}
else
{
?>
<body>
<?php
}

?>
<div class="left">
	<form id="request" action="../" method="post" target="hiddenResponse" enctype="multipart/form-data">
		<div class="left-content">
			<div class="attr">
				<label for="history">History: </label>
				<select id="history">
					<option>Select request</option>
				</select>
			</div>

			<div class="param">
				<label for="ks">KS (string):</label>
				<input id="ks" type="text" class="" name="ks" size="30" />
				<input id="chk-ks" type="checkbox" checked="checked" />
			</div>
			<?php
			if($indexConfig->additionals)
			{
				foreach($indexConfig->additionals as $fieldName => $additionalField)
				{
					?>
					<div class="param">
						<label for="<?php echo $fieldName; ?>"><?php echo $additionalField->title; ?> (<?php echo $additionalField->type; ?>):</label>
						<?php if($additionalField->values): ?>
							<select name="<?php echo $fieldName; ?>">
								<?php
								foreach($additionalField->values as $value)
								{
									echo "<option value=\"{$value->value}\">{$value->title}</option>";
								}
								?>
							</select>
						<?php else: ?>
							<input type="text" name="<?php echo $fieldName; ?>" size="30" value="<?php echo (isset($additionalField->value) ? $additionalField->value : ''); ?>" />
						<?php endif; ?>
						<input type="checkbox" <?php echo (isset($additionalField->checked) && $additionalField->checked ? 'checked="checked" class="alwaysEnabled"' : ''); ?> />
					</div>
				<?php
				}
			}
			?>

			<div id="dvService">
				<div class="attr">
					<label for="service">Select service:</label>
					<select name="service">
						<option value="">Select service</option>
						<option value="multirequest">Multirequest</option>
						<?php
						foreach($services as $serviceId => $serviceActionItem)
						{
							/* @var $serviceActionItem KalturaServiceActionItem */
							$serviceId = $serviceActionItem->serviceId;
							$serviceName = $serviceActionItem->serviceInfo->serviceName;
							$serviceLabel = $serviceActionItem->serviceInfo->serviceName;
							$pluginName = $serviceActionItem->serviceInfo->package;

							if ($pluginName)
								$serviceName = "$pluginName.$serviceName";

							if ($serviceActionItem->serviceInfo->deprecated)
								$serviceLabel .= ' (deprecated)';

							echo "<option value=\"$serviceId\" title=\"$serviceName\">$serviceLabel</option>";
						}
						?>
					</select>
					<img src="images/help.png" class="service-help help" />
				</div>
				<div class="attr" style="display: none">
					<label for="action">Select action:</label>
					<select name="action"></select>
					<img src="images/help.png" class="action-help help" title="" />
				</div>
				<div class="attr" style="display: none">
					<input type="button" class="add-request-button button" value="Add Request" />
				</div>

				<div class="action-params"></div>
				<div class="objects-containter"></div>
			</div>

			<div>
				<button type="submit">Send</button>
			</div>

			<?php

			if($indexConfig->get("logParser"))
			{
				?>
				<div id="dvLogParser">
					<div class="attr">
						<label for="action">HTTP Log:</label>
						<input type="file" />
						<img id="actionHelp" src="images/help.png" class="help" title="Supported format is HTTP Archive V1.1 (har file)" />
					</div>
					<div>
						<button onclick="kLogParser.load()">Load</button>
					</div>
				</div>
			<?php
			}

			?>
		</div>
	</form>
</div>
<div class="right">
	<iframe class="right-content" id="response" name="response" src="./testme.result.php" scrolling="no"></iframe>
	<iframe id="hiddenResponse" name="hiddenResponse" src=""></iframe>
</div>
<ul id="codeSubMenu">
	<li class="code-menu code-menu-php active"><a href="#"
												  onclick="switchToPHP()">PHP</a></li>
	<li class="code-menu code-menu-java"><a href="#"
											onclick="switchToJava()">Java</a></li>
	<li class="code-menu code-menu-csharp"><a href="#"
											  onclick="switchToCSharp()">C#</a></li>
	<li class="code-menu code-menu-python"><a href="#"
											  onclick="switchToPython()">Python</a></li>
	<li class="code-menu code-menu-javascript"><a href="#"
												  onclick="switchToJavascript()">Javascript</a></li>
	<li class="code-menu"><a class="code-menu-toggle" href="#"
							 onclick="toggleCode()" id="codeToggle">Show Code Example</a></li>
</ul>
<div class="code" id="codeExample" style="display: none;">
	<div id="disclaimer"><p>Note: The auto-generated code is for reference purposes and orientation. A direct copy&amp;paste will not work on its own. Please make sure to review the sample and adapt to your own application code.</p></div>
	<div id="example"></div>
</div>
<div id="httpSpy" style="display: none;">
	<div id="httpSpyForm">
		<input type="file" id="fileHttpSpy" />
		<input type="button" id="parseHttpSpy" value="Parse"  />
	</div>
</div>
</body>
</html>