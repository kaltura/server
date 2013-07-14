<!DOCTYPE html>
<!--[if IE 7]>         <html class="no-js lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie10 lt-ie9"> <![endif]-->
<!--[if lt IE 10]>     <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php echo include_http_metas() ?>
<?php echo include_metas() ?>

<?php echo include_title() ?>

<?php if (@$extraHead) echo $extraHead; ?>

<script type="text/javascript" src="<?php echo requestUtils::getCdnHost( requestUtils::getRequestProtocol() ); ?>/lib/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo requestUtils::getCdnHost( requestUtils::getRequestProtocol() ); ?>/lib/js/swfobject_v2.2.js"></script>

</head>
<body id="ng-app" ng-app="kmcApp">
 <div id="wrap">
 <?php echo $sf_content ?>
 </div>
<?php if( ! kConf::get('kmc_disable_analytics') ){ ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
try {
var pageTracker = _gat._getTracker("<?php echo kConf::get('ga_account'); ?>");
pageTracker._trackPageview();
} catch(err) {}
</script>
<?php } ?>
</body>
</html>