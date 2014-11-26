<!doctype html>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>
	<meta charset="utf-8" />
	<style>
	html, body { margin: 0; padding: 0; width: 100%; height: 100%; }
	body { background:#F8F8F8; font: 13px arial,sans-serif; }
	#wrapper { padding: 0 16px; }
	.left { float: left; text-align: left; width: 140px; margin: 5px 0; padding: 4px 0 0 0; }
	.right { float: left; text-align: left; margin: 5px 0 5px 0; padding: 0; }
	.current { padding-top: 4px; padding-left: 2px; color: #666666; }
	.note { font-size: 11px; color: #999999; }
	.center { text-align: center; }
	.clear { clear: both; }
	.error { color: #ff0000; font-weight: bold; font-size: 12px; margin-bottom: -10px; }
	input { font-size: 13px; width: 170px; }
	.truncated { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
	button { margin: 0 auto 5px; padding: 0 28px 0 0; height: 25px; border: 0; font: normal 11px arial,sans-serif; color:#2B2B2B; line-height: normal; overflow: visible; background: url(/lib/images/kmc/kmc_sprite.png) no-repeat -72px -152px; cursor: pointer; }
	button span { height:20px; padding: 4px 0 0 28px; margin: 1px 1px 0 0; float:left; white-space:nowrap; background:transparent url(/lib/images/kmc/kmc_sprite.png) no-repeat scroll 0 -153px;}
	button:hover span { background-position: 0 -178px;}
	@-moz-document url-prefix() {
   		button span { margin: -2px 2px 0 -3px; }
   		button { display: block; padding-right: 20px; }
   	}
	</style>
	<script>
	var getLocation = function(href) {
	    var l = document.createElement("a");
	    l.href = href;
	    return l;
	};
	var forceKMCHttps = <?php echo ($forceKMCHttps) ? 'true' : 'false'; ?>;
	// Check if our parent has the same protocol and host name and then check
	// for top / window objects
	if( forceKMCHttps && top != window && top.location.hostname != window.location.hostname ) { 
		top.location = window.location;
	}

	function focusFirstInput() {
		// check all the input in the form
		for(i=0; i < document.forms[0].length; i++)
		{
		  // check if input is not hidden & not disabled
		  if ( (document.forms[0][i].type != "hidden") && (document.forms[0][i].disabled != true) )
		  {
			document.forms[0][i].focus();
			break;
		  }
		}
	}
	</script>
</head>
<body onload="focusFirstInput();">
	<div id="wrapper">
		<?php if($type == 'password'){ ?>
		<form method="post" autocomplete="off"><br />
			<input type="hidden" name="do" value="password" />
			<div class="left">Current Password:</div>
			<div class="right"><input id="focused" type="password" name="cur_password" /></div>
			<br class="clear" />
			<div class="left">New Password:</div>
			<div class="right"><input type="password" name="new_password" /></div>
			<br class="clear" />
			<div class="left">Retry New Password:</div>
			<div class="right"><input type="password" name="retry_new_password" /></div>
			<br class="clear" /><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />
		</form>
		<?php } elseif($type == 'email'){ ?>
		<form method="post" autocomplete="off">
			<input type="hidden" name="do" value="email" />
			<div class="left">Current email address:</div>
			<div class="right current truncated" title="<?php echo $email; ?>"><?php echo $email; ?></div>
			<br class="clear" />
			<div class="left">Edit email address:</div>
			<div class="right"><input id="focused" type="text" name="email" value="<?php echo $email; ?>" /></div>
			<br class="clear" />
			<div class="left">Password:</div>
			<div class="right"><input type="password" name="password" /></div>
			<br class="clear" />
			<div class="note">* Password is required for editing your email address.</div><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />			
		</form>
		<?php } elseif($type == 'name') { ?>
		<form method="post" autocomplete="off">
			<input type="hidden" name="do" value="name" />
			<div class="left">Current name:</div>
			<div class="right current truncated" title="<?php echo $fname . ' ' . $lname; ?>"><?php echo $fname . ' ' . $lname; ?></div>
			<br class="clear" />
			<div class="left">Edit First Name:</div>
			<div class="right"><input type="text" name="fname" value="<?php echo $fname; ?>" /></div>
			<br class="clear" />
			<div class="left">Edit Last Name:</div>
			<div class="right"><input type="text" name="lname" value="<?php echo $lname; ?>" /></div>
			<br class="clear" />
			<div class="left">Password:</div>
			<div class="right"><input type="password" name="password" /></div>
			<br class="clear" />
			<div class="note">* Password is required for editing your name.</div><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />		
		</form>
		<?php } ?>
 	</div>
<?php if(isset($error)) { ?><script>alert(<?php echo json_encode($error); ?>);</script><?php } ?>
<?php if($success) { ?>
<script type="text/javascript" src="/lib/js/postmessage.js"></script>
<script type="text/javascript">
var parentUrl = "<?php echo $parent_url; ?>";
function send() {
	XD.postMessage("<?php echo $msg; ?>", decodeURIComponent(parentUrl), parent);
}
window.onload = send; 
</script>
<?php } ?>
 </body></html>