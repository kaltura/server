<?php

?>
<table style="font-family:arial; font-size:12px">
	<tr><td>Widget Creator</td></tr>	
	<tr><td>
	<table >
		<form method='post'>
			<input type="hidden" name="submitted" value="1">
			<tr><td><input type="checkbox" name="create" <?php echo  $create ? "checked='checked'" : "" ?>>Create new
				,Number of kshows to search: <input name="limit" id="limit" value="<?php echo  $limit ?>" size="5"></td></tr>
			<tr><td><input type="radio" name="method" value="partner" <?php echo  $method == "partner" ? "checked='checked'" : "" ?> >partner_id <input name="partner_id" id="partner_id" value="<?php echo  $partner_id ?>">
				</td>
			<tr>
			<tr><td><input type="radio" name="method" value="list" <?php echo  $method == "list" ? "checked='checked'" : "" ?> > kshow_ids 
				<input name="kshow_ids" id="kshow_ids" value="<?php echo  $kshow_ids ?>" size="50"></td></tr>
			
			<tr>
				<td>source_widget_id <input name="source_widget_id" id="source_widget_id" value="<?php echo  $source_widget_id ?>"><td>
			</tr>
			<tr><td><input type="submit" value="Create Widgets"></td></tr>
		</form>
	</table>
	</td></tr>
	<tr>
		<td>
			<textarea rows="30" cols="160">
<?php
foreach ( $res as $data ) { echo $data ;}
?>			
			</textarea>
		</td>
	</tr>	
	<tr><td>Errors:</td></tr>
	<tr>
		<td>
			<textarea rows="30" cols="160">
<?php
foreach ( $errors as $data ) { echo $data ;}
?>			
			</textarea>
		</td>
	</tr>			
</table>
