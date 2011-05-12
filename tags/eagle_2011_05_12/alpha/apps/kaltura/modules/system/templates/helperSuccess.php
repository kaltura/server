<?php
?>

<div style="font-family: arial; width: 90% ; margin: auto;">
<form method="post">
	Algorithm:
	<ul style='list-style:none; margin:10px; padding: 5px;'>
		<li><input type="radio" id="algo" name="algo" value="wiki_decode" <?php echo $algo == "wiki_decode" ? "checked='checked'" : "" ; ?> >Wiki Decode</li>
		<li><input type="radio" id="algo" name="algo" value="wiki_decode_no_serialize" <?php echo $algo == "wiki_decode_no_serialize" ? "checked='checked'" : "" ; ?> >Wiki Decode (No unserialize)</li>
		<li><input type="radio" id="algo" name="algo" value="base64_encode" <?php echo $algo == "base64_encode" ? "checked='checked'" : "" ; ?> >Base64 Encode </li>
		<li><input type="radio" id="algo" name="algo" value="base64_decode" <?php echo $algo == "base64_decode" ? "checked='checked'" : "" ; ?> >Base64 Decode </li>
		<li><input type="radio" id="algo" name="algo" value="base64_3des_encode" <?php echo $algo == "base64_3des_encode" ? "checked='checked'" : "" ; ?> >Base64 3des Encode   key:<input type="text" name="des_key" value="<?php echo  @$des_key ?>"></li>
		<li><input type="radio" id="algo" name="algo" value="base64_3des_decode" <?php echo $algo == "base64_3des_decode" ? "checked='checked'" : "" ; ?> >Base64 3des Decode  </li>		
		<li><input type="radio" id="algo" name="algo" value="ks" <?php echo $algo == "ks" ? "checked='checked'" : "" ; ?> >KS</li>
		<li><input type="radio" id="algo" name="algo" value="kwid" <?php echo $algo == "kwid" ? "checked='checked'" : "" ; ?> >kwid (wiki)   secret: <input type="text" name="secret" value="<?php echo  $secret ?>"></li> 
		<li><input type="radio" id="algo" name="algo" value="ip" <?php echo $algo == "ip" ? "checked='checked'" : "" ; ?> >ip to country</li>
	</ul>
		
	
	String to manipulate:<br>
	<textarea id="str" name="str" rows="5" cols="90"><?php echo $str ?></textarea>
	<br>
	 
	<input type="submit" name="submit" value="submit"/>
</form>

<br>
Length of input: <?php echo strlen ( $str ) ?><br>
Result:<br>
<pre style='text-align: left; background-color:lightyellow; padding: 10px; border: 1px solid #ccc;'>
<?php print_r ( $res ) ;?>
</pre>

</div>