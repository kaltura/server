<?php
if ( $result == 1 ) { ?>
<script type="text/javascript">
window.location = 	"<?php echo $sign_in_referer ?>";
</script>
<?php } elseif ( $result == 2 ) { ?>
You are now logged-in
<script type="text/javascript">
window.location = 	"/index.php/salestools";
</script>
<?php } ?>
<div style="font-family:arial; font-size:12px">
You requested <?php echo $sign_in_referer ?>
<form method="post" action="/index.php/salestools/login">
<input type="hidden" name="sign_in_referer" value="<?php echo $sign_in_referer ?>"/>
<input type="hidden" name="exit" value="false">
<table style="font-family:arial">
<?php if ( $result == -1 ) { ?>
	<tr><td colspan=2 style="color:red">Error. Please try again</td></tr>
<?php } ?>	
	<tr><td>Login:</td><td><input name="login" value="<?php echo $login ?>"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="pwd" value=""></td></tr>
	<tr><td></td><td><input type="submit" name="go"/></td></tr>
</table>
</form>
</div>