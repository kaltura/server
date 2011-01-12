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
			<?php if ($inputObject): ?>
			$(".left").scrollTo({top: 0, left: 0});
			$(".left").scrollTo($("#object_<?php echo $inputObject;?>"), 0, {axis:'y'});
			<?php endif; ?>
		});

		$(document).ready(function() {
			serviceListItems = $('li.service > a');
			for (i = 0; i < serviceListItems.length; i++)
			{
				if (serviceListItems[i].text.toLowerCase() == '<?php echo $inputService ?>')
				{
					serviceListItems[i].parentNode.className = 'service expended';
				}
			}
			});
	</script>
</head>
<body>
	<ul id="kmcSubMenu">
 	<li>
     <a href="../testme/index.php">Test Console</a>
    </li>
    <li class="active">
     <a href="#">API Documentation</a>
    </li>
   </ul>
