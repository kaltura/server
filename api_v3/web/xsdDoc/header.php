<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kaltura - XML Schema Documentation</title>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<script type="text/javascript" src="../testme/js/jquery-1.3.1.min.js"></script>
<script type="text/javascript" src="../testme/js/jquery.scrollTo-min.js"></script>
<script type="text/javascript">
		$(function() {
			$(window).resize(function(){
				$(".left").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
				$(".right").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
			});
			$(window).resize();
		});
	</script>
</head>
<?php

if(!isset($_REQUEST['hideMenu']) || !$_REQUEST['hideMenu'])
{
	?>
		<body class="body-bg">
		<ul id="kmcSubMenu">
			<li><a href="../testme/">Test Console</a></li>
			<li><a href="../testmeDoc/">API Documentation</a></li>
			<li class="active"><a href="#">XML Schema</a></li>
			<li><a href="../testme/client-libs.php">API Client Libraries</a></li>
		</ul>
	<?php
}
else 
{
	?>
		<body>
	<?php
}
