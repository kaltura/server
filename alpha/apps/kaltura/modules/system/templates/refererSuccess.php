<?php

?>
<div>
partner id: <?php echo $partner_id ?><br>
<table border=1 cellspacing='0px' cellpadding='4px'>
	<tr>
		<td>partner id</td><td>entry id</td><td>created at</td><td>referer</td>
	</tr>
<?php
foreach ( $widget_list as $widget ) { ?>
	<tr>
		<td><?php echo  $widget->getPartnerId() ?></td>
		<td><?php echo  $widget->getEntryId() ?></td>
		<td><?php echo  $widget->getCreatedAt() ?></td>
		<td><a target='_blank' href='<?php echo  $widget->getReferer() ?>'><?php echo  $widget->getReferer() ?></a></td>
	</tr>
<?php } ?>

</table>
</div>