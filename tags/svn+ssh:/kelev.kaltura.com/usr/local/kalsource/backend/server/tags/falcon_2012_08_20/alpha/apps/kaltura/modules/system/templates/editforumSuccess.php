<?php
include_once ( "_header.php" );
?>
Edit forum posts <br>
<?php if ( $error )  echo $error ;?>  
<?php echo ($modified_fields ? "Saved changes" : "No changes to save" ) ?><br>
<form method="post">
<table>

<tr>
	<td>ID</td>
	<td colspan=2> <?php echo $post->getId() ?><input type="hidden" name="post_id" value="<?php echo $post->getId() ?>"></td>
</tr>

<tr>
	<td></td>
	<td>What's about to be saved</td>
	<td>Before Save</td>
</tr>	 
<tr>
	<td>Title:</td>
	<td> <textarea name="post_title" rows="5" cols="50"><?php echo  $post->getTitle() ?></textarea></td>
	<td> <textarea name="" rows="5" cols="50"><?php echo  $before_changes->getTitle() ?></textarea></td>
</tr>

<tr>
	<td>Content:</td>
	<td><textarea name="post_content" rows="15" cols="50"><?php echo $post->getContent()  ?></textarea></td> 
	<td><textarea name="" rows="15" cols="50"><?php echo $before_changes->getContent()  ?></textarea></td>
</tr>	
 
<tr>
	<td colspan=3 style="text-align: center;"><input type="submit" name="submit" value="submit"></td>
</tr> 

</table>

</form>

