<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Kaltura - API v3 SDK - Client Libraries</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="Description" content="The Kaltura API SDK is a set of automatically generated API Client Libraries in native programming languages that simplifies development of applications that leverage the Kaltura Platform API" />
	<meta name="Keywords" content="Kaltura,Kaltura API,API,SDK,Client Libraries,client library,code library,library,video,testme" />
	<meta name="author" content="Kaltura Inc." />
	<link rel="stylesheet" type="text/css" href="css/main.css" />
	<link rel="stylesheet" type="text/css" href="css/sdk-page.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.tweet.css" media="all" />
	<script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.tweet.js" type="text/javascript"></script>
	
	<script type='text/javascript'>
		function gotoTestMe() {
			var isInIFrame = (window.location != window.parent.location) ? true : false;
			if (isInIFrame) {
				window.top.location = '/admin_console/index.php/index/testme';
			} else {
				window.location = '/api_v3/testme/index.php';
			}
		}
	</script>
</head>
<?php 
	require_once(__DIR__ . "/../../bootstrap.php");
	
	//Get the generated clients summary
	$root = myContentStorage::getFSContentRootPath();
	
	$summary = array();
	$schemaGenDate = '';
	$apiVersion = '';
	
	if(file_exists("$root/content/clientlibs/summary.kinf"))
	{
		$summaryData = file_get_contents("$root/content/clientlibs/summary.kinf");
		$summary = unserialize($summaryData);
		
		$schemaGenDate = $summary['generatedDate'];
		$apiVersion = $summary['apiVersion'];
		
		unset($summary['generatedDate']);
		unset($summary['apiVersion']);
	}
?>
	<?php
	if(!isset($_REQUEST['hideMenu']) || !$_REQUEST['hideMenu'])
		{
			?>
			<body class="body-bg">
				<ul id="kmcSubMenu">
					<li><a href="index.php">Test Console</a></li>
					<li><a href="../testmeDoc/index.php">API Documentation</a></li>
					<li class="active"><a href="#">API Client Libraries</a></li>
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
		<div id="content">
			<div id="header">
				<h1>Kaltura API SDK - Native Client Libraries</h1>
				<p>When developing applications that interact with the Kaltura API, it is best to use a native Client Library.</p>
				<p style="margin-bottom:5px;">Below you can download the Client Library for the programming language of your choice.</p>
				<p>Please note, the client libraries provided here have been generated for the Kaltura.com SaaS API.<br />
				Users of self-hosted editions of Kaltura (On-Prem or CE) are encouraged to use their Kaltura Server Clients Generator for optimal compatibility.</p>
				<p>To learn how to use the client libraries and see example code implementations, use the <a href="#" onclick="gotoTestMe();">API Test Console</a>.<br />
				The Test Console automatically generates sample code based on the API actions and parameters you select.</p>
			</div>
			<div id="boxs">
				<div id="downloads-box">
					<h2>Download latest client libraries</h2>
					<p class="graytext">API version: <?php echo $apiVersion; ?> | API Schema date: <?php echo $schemaGenDate; ?></p>
					<div id="download-buttons" >
						<div>
							<?php 
							$buttsInLine = 4;
							$current = 0;
							foreach($summary as $clientName => $params)
							{
								?>
									<div class="download-button <?php echo $clientName; ?>-btn">
										<a href="http://<?php echo kConf::get('cdn_host'); ?>/content/clientlibs/<?php echo $clientName.'_'.$schemaGenDate; ?>.tar.gz" target="_blank" title="Single class <?php echo $clientName; ?> client library" class="download-btn">
										</a>
										<?php if (isset($params['linkhref']) && isset($params['linktext']))
											echo '<a href="' . $params['linkhref'] . '">' . $params['linktext'] . '</a>';
										?>										
									</div>
								<?php 
								
								++$current;
								if(!($current % $buttsInLine))
									echo '<div class="clear"></div>';
							}
							
							?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="notifications-box">
					<div id="notifications-header">
						<strong>Missing a language?</strong>
						<p style="margin-top:4px;">Tweet - <a class="bluelink" target="_blank" href="http://twitter.com/?status=@Kaltura_API%20pls%20add%20sdk%20for%3A%20%5Bprogramming_language%5D" title="Tweet for a new client library">@Kaltura_API pls add sdk for: [lang]</a>.<br />
						Or <a class="bluelink" target="_blank" href="http://www.kaltura.org/api-client-library-generator-guide" title="Creating a client library generator script">create your own Client Library Generator</a>.</p>
					</div>
					<div class="twitter-icon-div">
						<a href="http://twitter.com/Kaltura_API" target="_blank" title="Follow @Kaltura_API to get updates about changes and new additions to the API and Client Libraries (SDK)."><img class="twitter-icon" src="./images/twitter-icon.png" /></a>
						<span class="graytext">Follow <a href="http://twitter.com/Kaltura_API" target="_blank" title="Follow @Kaltura_API to get updates about changes and new additions to the API and Client Libraries (SDK).">@kaltura_api</a> for updates</span>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<p class="graytext">To help developers leverage the Kaltura APIs in their own native programming language,<br />
Kaltura has created a mechanism that automatically generates up-to-date software development kits in various languages.<br />
Every time the API is updated, all of the client libraries listed above will be updated too.<br /></p>
		</div>
<?php 
	if (kConf::hasParam("testme_tracking_code"))
	{
		require(kConf::get("testme_tracking_code"));
	}
?>
	</body>
</html>
