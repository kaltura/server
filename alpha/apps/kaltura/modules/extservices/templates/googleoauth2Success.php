<?php if ($subAction == googleoauth2Action::SUB_ACTION_LOGIN_SCREEN): ?>
	<?php if ($invalidConfig): ?>
		Service not found
	<?php elseif ($loginError): ?>
		Login error
	<?php else: ?>
		<form id="google-oauth-form" method="post">
			<div class="error"></div>
			<label><span>Email:</span><input type="text" name="email" /></label>
			<label><span>Password:</span><input type="password" name="password" /></label>
			<button id="login" type="submit">Login</button>
		</form>
		<script type="text/javascript">
			var googleOAuth2PageOptions = {
				serviceUrl: '<?php echo $serviceUrl; ?>',
				nextUrl: '<?php echo $nextUrl ?>'
			};
			$googleOAuth2Page = new $.GoogleOAuth2Page($('#google-oauth-form'), googleOAuth2PageOptions);
		</script>
	<?php endif; ?>
<?php elseif ($subAction == googleoauth2Action::SUB_ACTION_REDIRECT_SCREEN): ?>
	<?php if ($ksError): ?>
		Invalid parameter(s)
	<?php else: ?>
		<a href="<?php echo $oauth2Url; ?>">Proceed to Google for authorization</a>
	<?php endif; ?>
<?php elseif ($subAction == googleoauth2Action::SUB_ACTION_PROCESS_OAUTH2_RESPONSE): ?>
	<?php if ($ksError): ?>
		Invalid parameter(s)
	<?php elseif ($tokenError): ?>
		Invalid token
	<?php endif; ?>
<?php elseif ($subAction == googleoauth2Action::SUB_ACTION_STATUS): ?>
	<?php if ($paramsError || $ksError): ?>
		Invalid parameter(s)
	<?php elseif ($tokenError): ?>
		Failed to verify access token
	<?php else: ?>
		Access token was verified successfully
	<?php endif; ?>
<?php endif; ?>