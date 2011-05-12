<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <!-- base href="<?php echo requestUtils::getRequestHost() ?>/index.php/kmc/help" -->
 <?php echo include_http_metas() ?>
 <?php echo include_metas() ?>
 <?php echo include_title() ?>
 <?php if (@$extraHead) echo $extraHead; ?>
</head>
<body>
 <div id="wrap">
 <?php echo $sf_content ?>
 </div>
 
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("<?php echo kConf::get('ga_account'); ?>");
	pageTracker._trackPageview();
	} catch(err) {}
</script>

</body>
</html>