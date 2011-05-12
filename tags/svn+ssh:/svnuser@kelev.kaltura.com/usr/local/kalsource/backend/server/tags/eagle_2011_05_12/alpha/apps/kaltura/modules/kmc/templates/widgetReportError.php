<div style='font-family: verdana; font-size: 12px;  text-indent: 50px'>
 
<br><br>

<form method="post">
<table border=0>
<tr><td colspan=2><?php echo $err ?></td></tr>


<tr><td colspan=2>
	<input type="hidden" name="ks_str" value="<?php echo $ks_str ?>">
	<input type="hidden" name="act" value="login">
</td></tr>
<tr>
	<td>Email:</td>
	<td><input type="text" name="email" value="<?php echo $email ?>"></td>
</tr>
<tr>
	<td>Password:</td>
	<td><input type="password" name="password"></td>
</tr>
<tr><td colspan=2 style="text-align:center;"><input type="submit" style='color:black; width:60px' name="login" value="login" /></td></tr>

</table>
</form>
</div>