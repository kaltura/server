<?php if ($subAction == FacebookConstants::SUB_ACTION_LOGIN_SCREEN): ?>
	<?php if ($loginError): ?>
		Login error
	<?php else: ?>
		<form id="facebook-oauth-form" method="post">
			<div class="error"></div>
			<label><span>Email:</span><input type="text" name="email" /></label>
			<label><span>Password:</span><input type="password" name="password" /></label>
			<button id="login" type="submit">Login</button>
		</form>
		<script type="text/javascript">
			var facebookOAuth2PageOptions = {
				serviceUrl: '<?php echo $serviceUrl; ?>',
				nextUrl: '<?php echo $nextUrl ?>'
			};
			$facebookOAuth2Page = new $.FacebookOAuth2Page($('#facebook-oauth-form'), facebookOAuth2PageOptions);
		</script>
	<?php endif; ?>
<?php elseif ($subAction == FacebookConstants::SUB_ACTION_REDIRECT_SCREEN): ?>
	<?php if ($ksError): ?>
		Invalid parameter(s)
	<?php elseif ($partnerError): ?>
		Using wrong partner for login
	<?php else: ?>
		<a href="<?php echo $oauth2Url; ?>">Proceed to Facebook for authorization</a>
	<?php endif; ?>
<?php elseif ($subAction == FacebookConstants::SUB_ACTION_PROCESS_OAUTH2_RESPONSE): ?>
	<?php if ($tokenError): ?>
		<?php echo $errorMessage; ?>
	<?php else: ?>
		Access token generated successfully
	<?php endif; ?>
<?php else: ?>
	Something went wrong. Please retry.
<?php endif; ?>