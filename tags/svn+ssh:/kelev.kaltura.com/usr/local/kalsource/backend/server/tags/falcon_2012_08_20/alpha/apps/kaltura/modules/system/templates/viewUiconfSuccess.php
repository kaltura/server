<?php

function addRow($uiconf, $even_row )
{
	$s = '<tr ' . ( $even_row ? 'class="even" ' : '' ). '>'.
		'<td><a href="'.url_for("system/editUiconf?id=".$uiconf->getId()).'">'.$uiconf->getId().'</a></td>'.
		'<td>'.$uiconf->getObjType().'</td>'.
		'<td>'.$uiconf->getPartnerId().'</td>'.
		'<td>'.$uiconf->getSubpId().'</td>'.
		'<td style="text-align:left;">'.
	 		$uiconf->getName().
 		'</td>'.
		'<td>'.$uiconf->getConfFilePath() . '</td>'.
		'<td>'.$uiconf->getWidth() . '</td>'.
		'<td>'.$uiconf->getHeight() . '</td>'.
		'<td>'.$uiconf->getSwfUrl() . '</td>'.
		'<td>'.$uiconf->getCreatedAt() . '</td>'.
		'<td>'.$uiconf->getUpdatedAt() . '</td>'.
		'<td>'.$uiconf->getTags() . '</td>'.
		'</tr>';
	 
	return $s;
}


$i=0;
$media_content = "";
foreach($uiconfs as $uiconf)
{
	 $media_content .= addRow($uiconf, ( $i % 2 == 0 ) );
	++$i;
}


?>

<script type="text/javascript">

function openInNewWindow ( url )
{
	h = window.open ( url );
	return ;
}

</script>

<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div>
		<form action="<?php echo url_for("system/viewUiconf"); ?>" method="get">
			<?php if ($partner): ?>
				<?php echo $partner->getName(); ?> (<?php echo $partner->getId(); ?>)
				<input type="submit" value="Change" />
			<?php else: ?>
				Partner ID: <input type="text" name="partnerId" value="<?php echo $partnerId; ?>" size="6" />
				<input type="submit" value="Go" />
			<?php endif; ?>
			<a href="<?php echo url_for("system/addUiconf"); ?>">Add New UI Conf</a>
		</form>
		<br />
		<br />
	</div>
	<div class="content">
		<div class="middle">	
				<table cellspacing="0" cellpadding="0" style="table-layout: auto;">
					<thead>
						<tr>
							<td class="type" style="width: 40px;" ><span>Id</span></td>
							<td class="type" ><span>Type</span></td>
							<td class="type" ><span>Partner</span></td>
							<td class="type" ><span>Subp</span></td>
							<td class="type" style="width:200px;"><span>Name</span></td>
							<td class="type" style="width:300px;"><span>Path</span></td>
							<td class="type" ><span>Width</span></td>
							<td class="type" ><span>Height</span></td>
							<td class="type" ><span>SWF</span></td>
							<td class="type" ><span>Created</span></td>
							<td class="type" ><span>Updated</span></td>
							<td class="type" ><span>Tags</span></td>
						</tr>
					</thead>
					<tbody id="media_content">
						<?php echo $media_content ?>
					</tbody>
				</table>
		</div><!-- end middle-->
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->
