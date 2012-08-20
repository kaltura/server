<?php

?>
<div style="font-family: arial; font-size: 13px;">
<a href="/index.php/system/login?exit=true">logout</a> Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>
<form>
	Entry Id: <input type="text" name="entry_id" value="<?php echo $entry_id ?>">
	
	Kshow Id: <input type="text" name="kshow_id" value="<?php echo $kshow_id ?>">
	<a href="./investigate?kshow_id=<?php echo $kshow_id ?>&entry_id=<?php echo $entry_id ?>">investigate</a>
<br>	
<?php if ( !empty ( $metadata ) ) { ?>

Pending string: <input type="input" id="pending" name="pending" value="<?php echo $pending ?>"/>
Remove Pending all together: <input type="checkbox" id="remove_pending" name="remove_pending" />
<?php } ?>
	
<input type="submit" id="Go" name="Go" value="Go"/>


</form>

</div>

<pre style="background-color: lightyellow; width: 80%;">
<?php echo kString::xmlEncode( $metadata ) ?>
</pre>
