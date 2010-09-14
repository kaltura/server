
<div>

<form method=post>
	entry ids<br/>
	<textarea name='entry_ids' rows=20 cols=50><?php echo $entry_ids ?></textarea>
	<br/>
	conversion profile id <input name='conversion_profile_id' value='<?php echo $conversion_profile_id ?>'>
	<br/> 
	priority (1=hi ... 5=low) <input name='priority' value='<?php echo $priority ?>'>
	<br/>
	<button>Reconvert</button>
</form>
</div>
<table border=0 >
<tr>
	<td>entry id</td>
	<td>partner id</td>
	<td>convert job id</td>
	<td>error</td>
</tr>
<?php

if ( $result )
{
	$i = 0;
	foreach ( $result as $job_data )
	{
		$col = $i %2 ? "rgb(224,224,224)" :  "rgb(248,248,248)" ; 
		list ( $entry_id , $entry , $dbBatchJob , $error ) = $job_data;
		echo "<tr style='background-color:$col'>" .
			"<td>$entry_id</td>" .
			"<td>" . ( $entry ? $entry->getPartnerId() : "?" ) . "</td>" .
			"<td>" . ( $dbBatchJob ? $dbBatchJob->getId() : "?" ) . "</td>" .
			"<td>$error</td>" .
			"</tr>";
		$i++;
	}
}
?>
</table>