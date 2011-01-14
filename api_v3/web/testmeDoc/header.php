<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kaltura - TestMe Console Documentation</title>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<script type="text/javascript" src="../testme/js/jquery-1.3.1.min.js"></script>
<script type="text/javascript" src="../testme/js/jquery.scrollTo-min.js"></script>
<!-- <script type="text/javascript" src="js/main.js"></script> -->
<script type="text/javascript">
		$(function() {
			$(window).resize(function(){
				$(".left").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
				$(".right").css("height", $("body").outerHeight() - $("#kmcSubMenu").outerHeight({ "margin": true }) - 10);
			});
			$(window).resize();
			<?php if ($inputObject) { ?>
			$(".left").scrollTo({top: 0, left: 0});
			$(".left").scrollTo($("#object_<?php echo $inputObject;?>"), 0, {axis:'y'});
			<?php } ?>
			<?php if ($inputService) { ?>
			$(".left").scrollTo({top: 0, left: 0});
			$(".left").scrollTo($("#service_<?php echo $inputService;?>"), 0, {axis:'y'});
			<?php } ?>
		});

		<?php if ($inputService) { ?>
		$(document).ready(function() {
			$('#service_<?php echo $inputService ?>').addClass('service expended');			
		});
		<?php } ?>
	</script>
</head>
<?php

if(!isset($_REQUEST['hideMenu']) || !$_REQUEST['hideMenu'])
{
	?>
		<body class="body-bg">
		<ul id="kmcSubMenu">
			<li><a href="../testme/index.php">Test Console</a></li>
			<li class="active"><a href="#">API Documentation</a></li>
		</ul>
	<?php
}
else 
{
	?>
		<body>
	<?php
}
