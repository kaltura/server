	<?php
		if(isset($_GET['style']) && $_GET['style'] == 'v') {	// kmc virgo
			$closeFunction = 'parent.kmcCloseModal()';
			$bodyBgColor = 'E1E1E1';
		}
		else {
			$closeFunction = 'parent.kmc.utils.closeModal()';
			$bodyBgColor = 'F8F8F8';
		}
	?>

<html>
	<head>
		<title>Support Request</title>
		<!--script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script-->
		<style>
		  body { background:#<?php echo $bodyBgColor; ?>;}
			div#wrapper { } /* for ie */
			em { font-size:11px; font-style:italic;}
			form { padding: 15px; #margin: -20px 0 0; background:#<?php echo $bodyBgColor; ?>}
			 form * { font: normal 12px arial,sans-serif;}
			 fieldset { margin-bottom:20px; #margin-bottom:0; padding: 20px 15px 8px 12px; -moz-border-radius:6px; -webkit-border-radius:6px; }
			  legend { font-weight:bold; #margin-bottom:20px;}
			  fieldset p { margin:0;}
			  label { margin-bottom:15px; clear:both; display:block;}
			   label img { margin-top:-2px; #margin-top:-16px; float:right; display:none;}
				label.error img { display:inline !important;}
				 label#checkbox.error img { position:relative; #margin: -23px 4px 0px 0px; top:3px; left:-300px;}
				label.error input, label.error textarea, label.error select { border: solid 1px red; color:red;}
				label.error em  { color:red;}
			  input, textarea, select { float:right;}
			   input[type="text"], input.text, input.file {width:300px; #height:24px; padding: 3px 2px 2px; margin-top:-5px; #margin-top:-20px;}
			   input[type="checkbox"], input.checkbox { width:auto; margin-left:63px; vertical-align:-2px; float:none;}
			  select { width:300px; padding: 2px 2px 2px 1px; margin: -4px 0 10px; #margin-top:-18px;}
			  textarea { width:300px; padding-left:3px; margin: -2px 0 12px; #margin: -18px 0 -2px;}
			  fieldset div { height:34px; margin-left:155px; display:none;}
			   fieldset div * { font-size:11px;}
			   fieldset div label { display:inline;}
			   fieldset div input { width:86px !important; #height:18px; padding:0 !important; margin:0 !important; float:none;}
			 div#submit { text-align:center}
			 button { height:24px; padding: 0 13px 1px; border: solid 1px #B7BABC; margin: 11px auto 5px; #margin-top:21px; display:block;
					 background: url(/lib/images/kmc/kmc_sprite.png) no-repeat -57px -153px; /* -94px -152px */ cursor:pointer; color:#2B2B2B;}
			  button:hover { Xbackground-position: -94px -177px; color:#000; border-color:#5E5E5E;}

			  div#thx { margin: 2px 20px 0 2px; font: normal 12px arial,sans-serif;}
			  div#free { margin:2px; text-align:center; font: normal 14px arial,sans-serif;}
			  div#thx button, div#free button { margin-top:25px;}
			  div#thx button { margin-right:214px;}
		</style>
	</head>
	<body>

<?php
if(isset($_GET['type']) && $_GET['type'] == md5('true')) {
	$pid = $_GET['pid'];
	$email = $_GET['email'];
	?>

<script type="text/javascript">
	$(function() {

		if($.browser.msie) {
			$("input[type='text']").addClass("text");
			$("input[type='file']").addClass("file");
			$("input[type='checkbox']").addClass("checkbox");
		}

		$("#your_name").focus();

		$website = $("#website");
		$website.change(function() {
			if($website.value != "") {
				$("#login_info").show();
//				var iframe_height = $.browser.msie ? 751: ($.browser.safari ? 690 : 765);
				var iframe_height = $.browser.safari ? 730 : 765;
				window.parent.document.getElementById("support").style.height = iframe_height + "px";
			}
			else
				$("#login_info").hide();
		});

		$("#support").submit(function(){
			window.$fields = window.$fields || $("label");
			window.error_flag = false;
			window.focus_flag = false;
			window.$fields.each(function(){
				var $this_label = $(this);
				var $this_field = $this_label.find(":input");
				if($this_field.hasClass("required")) {
					window.required_flag = false;
					$this_label.removeClass("error");
					if($this_field.val() == "" || ($this_field.is(":checkbox") && !$this_field.is(":checked"))) {
						$this_label.addClass("error");
						if(!window.focus_flag) {
							$this_field.focus();
							window.focus_flag = true;
						}
						window.error_flag = true;
						window.required_flag = true;
					}
					if($this_field.attr("id") == "email") {
						window.email_flag = false;
						var the_email = $this_field.val();
						var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
						//							if(!((the_email.indexOf(".") > 2) && (the_email.indexOf("@") > 0))) {
						if(!regex.test(the_email)) {
							window.error_flag = true;
							window.email_flag = true;
						}
					}
				} // end required
			});
			if(window.error_flag) {
				var required_error = window.required_flag ? "Please fill in all required fields.     \n" : "" ;
				var email_error = window.email_flag ? "Please provide a valid email.     \n" : "" ;
				alert(required_error + email_error);
				return false;
			}
			else{
				$("#subject").val(<?php echo $pid; ?> + " ~ " + $("#feature").val() + " ~ " + $("#cms_extension").val() + " ~ " + $("#problem").val());
			}
		});
	}); // onReady
</script>
	<?php
	if($sent_request) {
		?>
<div id="thx">
	<p>Thank you for your submission.</p>
	<p>Your support request has been submitted and will be handled by one of our support specialists as soon as possible (you should receive a confirmation email with a ticket number shortly).</p>
	<p>In the mean time, you may wish to <a href="http://www.kaltura.org/search" target="_blank">browse the forums, guides and documentation on&nbsp;kaltura.org</a></p>

	<button onClick="<?php echo $closeFunction; ?>;">OK</button>
</div>
<script type="text/javascript" >
	window.parent.document.getElementById("support").height = 190;
</script>
	<?php
	}
	else {
		?>

<div id="wrapper">
	<form action="" method="post" enctype="multipart/form-data" name="support" id="support">
		<input type="hidden" name="subject" id="subject" value="" />
		<fieldset>
			<legend>Your Info</legend>
			<p><label>* Partner Id: <input type="text" class="required" name="partner_id" id="partner_id" value="<?php echo $pid; ?>" /><img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
            <p><label>* Your Name: <input type="text" class="required" name="your_name" id="your_name" /><img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
            <p><label>* Email: <input type="text" class="required" name="email" id="email" value="<?php echo $email; ?>" /><img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
            <p><label id="checkbox">* Confirm email: <input type="checkbox" class="required" id="confirm_email" value="confirmed" /><em>&nbsp;this is the (only) email to which we will respond</em><img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
		</fieldset>

		<fieldset>
			<legend>The Problem</legend>
			<p><label>* Component/ Feature:
					<select name="feature" id="feature" class="required">
						<option selected="selected" value="">Please select...</option>
						<option value="Playback">Playback</option>
						<option value="Uploading">Uploading</option>
						<option value="Editing">Editing</option>
						<option value="API/ Integration">API/ Integration</option>
						<option value="Infrastructure/ Communication/ Network">Infrastructure/ Communication/ Network</option>
						<option value="Analytics">Analytics</option>
						<option value="Kaltura Management Console Access">Kaltura Management Console Access</option>
						<option value="Ads">Ads</option>
						<option value="Kaltura Admin Console">Kaltura Admin Console</option>
						<option value="Other">Other</option>
					</select>
					<img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
			<p><label>* Affected Functionality:
					<select name="problem" id="problem" class="required">
						<option selected="selected" value="">Please select...</option>
						<option value="Experiencing quality issues">Experiencing quality issues</option>
						<option value="Does not work as expected">Does not work as expected</option>
						<option value="Not working at all">Not working at all</option>
						<option value="Other">Other</option>
					</select>
					<img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" /></label></p>
			<p>
				<label>* Description:
					<textarea name="description" id="description" cols="45" rows="5" class="required"></textarea>
					<img src="/lib/images/kmc/error.jpg" height="20" width="20" alt="error" />
				</label>
			</p>
			<p>
				<label>Steps to Reproduce:
					<textarea name="steps" id="steps" cols="45" rows="5"></textarea>
				</label>
			</p>
			<p>
				<label>Sample Link:
					<input type="text" name="link" id="link" />
				</label>
			</p>
<!--			<p>
			  <label>Upload File/ Screenshot:<input type="file" name="upload" id="upload" /></label>
			</p>-->
		</fieldset>

		<fieldset>
			<legend>Your Setup</legend>
			<p>
				<label>Website URL:
					<input type="text" name="website" id="website" />
				</label>
			</p>
			<div id="login_info">
				<label>User name:
					<input type="text" name="user_name" id="user_name" />
				</label>
				&nbsp; <label>Password: <input type="text" name="password" id="Password" /></label>
			</div>
			<p><label>Kaltura CMS Extension:
					<select name="cms_extension" id="cms_extension">
						<option value="None" selected="selected">None</option>
						<option value="Drupal Module">Drupal Module</option>
						<option value="Joomla Extension">Joomla Extension</option>
						<option value="Moodle Extension">Moodle Extension</option>
						<option value="Elgg Extension">Elgg Extension</option>
						<option value="Wiki Extension">Wiki Extension</option>
						<option value="WordPress Plugin">WordPress Plugin</option>
						<option value="Other">Other</option>
					</select></label></p>
		</fieldset>
		<div id="submit"><button type="submit"><span>Send</span></button></div>
	</form>
	<p>&nbsp;</p>
</div><!--wrapper-->
	<?
	}
}
else {
	?>
<div id="free">
	<p>Go to kaltura.org for <a target="_blank" href="http://www.kaltura.org/search">great community support</a></p>
	<p><a target="_blank" href="http://corp.kaltura.com/about/contact?subject=Upgrade">Contact us</a> to learn more about our<br />paid Professional Services packages</p>
	<button onClick="<?php echo $closeFunction; ?>;">OK</button>
</div>
<script type="text/javascript" >
	window.parent.document.getElementById("support").height = 150;
</script>
<?php
}
?>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
		var pageTracker = _gat._getTracker("<?php echo kConf::get('ga_account'); ?>");
		pageTracker._trackPageview();
	}
	catch(err) {}
</script>
	</body>
</html>