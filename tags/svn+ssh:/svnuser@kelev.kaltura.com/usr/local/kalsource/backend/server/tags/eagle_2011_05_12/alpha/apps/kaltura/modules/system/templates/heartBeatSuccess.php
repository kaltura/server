
<table>
	<tr><td>Bottom Line</td><td><?php echo $bottom_line ?></td></tr> 
<?php
	
	
	foreach ( $test_array as $test => $result)
	{
		echo "<tr><td>$test</td><td>$result</td></tr>"; 
	}

?>

</table>