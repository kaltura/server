<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php echo include_http_metas() ?>
<?php echo include_metas() ?>

<?php echo include_title() ?>

<?php if (@$extraHead) echo $extraHead; ?>

</head>
<body>
	<div id="wrap" style="padding-top:50px">
	<?php echo $sf_content ?>
	</div>
</body>
</html>
