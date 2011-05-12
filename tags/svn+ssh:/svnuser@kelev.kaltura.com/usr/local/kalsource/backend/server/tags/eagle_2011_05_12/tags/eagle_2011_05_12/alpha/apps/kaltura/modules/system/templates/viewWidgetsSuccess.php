<?php

require_once ( "mySmartPagerRenderer.class.php" );

function addRow($widget_log, $kshow_data, $entry_data, $even_row )
{
	if ( $widget_log instanceof WidgetLog )
	{
		$widget_log = new genericObjectWrapper ( $widget_log , true );
	}
	
	$contributors = $kshow_data ? $kshow_data->getContributors() : 0;
	$entries = $kshow_data ? $kshow_data->getEntries() : 0;
	
	$plays = $entry_data ? $entry_data["plays"] : 0;
	$views = $entry_data ? $entry_data["views"] : 0;
	
	$entry_link = '/index.php/browse?entry_id=' .$widget_log->entryId ;
	$referer_link = '<a href="javascript:openInNewWindow ( \'' . $widget_log->referer . '\'  );">' . $widget_log->referer . '</a>';
	
	
	$s = '<tr ' . ( $even_row ? 'class="even" ' : '' ). '>'.
		'<td>'.$widget_log->id.'</td>'.
	 	'<td>'.$widget_log->kshowId.'</td>'.
	 	'<td class="info"><a href="'.$entry_link.'">'.$widget_log->entryId . " " . $widget_log->entry->name . '</a></td>'.
	 	'<td style="text-align:left;">' . $referer_link . '</td>'.
		'<td>'.$widget_log->createdAt . '</td>'.
	 	'<td>'.$widget_log->views.'/'.$views . '</td>'.
		'<td>'.$widget_log->plays.'/'.$plays . '</td>'.
		'<td>'.$contributors . '</td>'.
		'<td>'.$entries . '</td>'.
		'<td>'.$widget_log->ip1AsText . '</td>'.
		'<td>'.$widget_log->ip1Count . '</td>'.
	 '</tr>';
	 
	return $s;
}


$htmlPager = mySmartPagerRenderer::createHtmlPager( $lastPage , $page );


$i=0;
$media_content = "";

// fetch #contribs and #entries per kshow
$kshow_ids = array();
$entry_ids = array();
foreach($widget_log_list as $widget_log)
{
	$kshow_id = $widget_log->getKshowId();
	if ($kshow_id)
		$kshow_ids[] = $kshow_id;
		
	$entry_id = $widget_log->getEntryId();
	if ($entry_id)
		$entry_ids[] = $entry_id;
}

$c = new Criteria();
$c->add(kshowPeer::ID, $kshow_ids, Criteria::IN);
$kshows = kshowPeer::doSelect($c);


$kshows_data = array();
foreach($kshows as $kshow)
	$kshows_data[$kshow->getId()] = $kshow;

// fetch sum of plays per entry_id
$c = new Criteria();
$c->addSelectColumn(WidgetLogPeer::ENTRY_ID);
$c->addSelectColumn("SUM(".WidgetLogPeer::PLAYS.")");
$c->addSelectColumn("SUM(".WidgetLogPeer::VIEWS.")");
$c->add(WidgetLogPeer::ENTRY_ID, $entry_ids, Criteria::IN);
$c->addGroupByColumn(WidgetLogPeer::ENTRY_ID);
$rs = WidgetLogPeer::doSelectStmt($c);

$entries_data = array();

$res = $rs->fetchAll();
foreach($res as $record) 
{
	$entry_id = $record[0];
	$plays= $record[1];
	$views = $record[2];
	$entries_data[$entry_id] = array("plays" => $plays, "views" => $views);
}
		
//old code from doSelectRs
//while($rs->next())
//{
//	$entry_id = $rs->getInt(1);
//	$plays= $rs->getInt(2);
//	$views = $rs->getInt(3);
//	$entries_data[$entry_id] = array("plays" => $plays, "views" => $views);
//}
	
foreach($widget_log_list as $widget_log)
{
	 $media_content .= addRow($widget_log, @$kshows_data[$widget_log->getKshowId()], @$entries_data[$widget_log->getEntryId()], ( $i % 2 == 0 ) );
	++$i;
}

if ($firstTime) {
	
	$options = dashboardUtils::partnerOptions ( $partner_id );

?>

<script type="text/javascript">

jQuery(document).ready(function(){
	mediaSortOrder = "views";
	var defaultMediaPageSize = 100;
	mediaPager = new ObjectPager('media', defaultMediaPageSize, requestMediaWidget );
	updatePagerAndRebind ( "media_pager" , null , requestMediaPageWidget );
	updatePagerAndRebind ( "media_pagerB" , null , requestMediaPageWidget );
	
jQuery("#referer")
	.keydown(function(e){
		if (e.keyCode == 13){
			requestMediaWidget(mediaPager, 1);
		}
	})
	

}); // end document ready


function openInNewWindow ( url )
{
	h = window.open ( url );
	return ;
}

</script>

<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div>
	</div>
	<div class="content">
		<div class="top">
			<div class="clearfix" style="margin:10px 0;">
				<ul class="pager" id="media_pager" style="float:right; margin:0;">
				<?php echo	$htmlPager ?>
				</ul>
				<input type="text" id="referer" value="<?php echo $referer; ?>"/>
				<span style="padding: 0 8px">Partner:</span>
				<select onchange="partnerSelect(this)" id="partner_id">
					<?php echo $options; ?>
				</select>
			</div>
		</div><!-- end top-->
		<div class="middle">	
				<table cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<td class="type" style="width: 40px;" onclick='changeMediaSortOrder (this, "+id")'><span>Id</span></td>
							<td class="type" onclick='changeMediaSortOrder (this, "+kshow_id")'><span>KshowId</span></td>
							<td class="type" onclick='changeMediaSortOrder ( this, "+entry_id")'><span>EntryId</span></td>
							<td class="info" style="text-align:center;" onclick='changeMediaSortOrder (this, "referer")'><span>Referrer</span></td>
							<td class="type" onclick='changeMediaSortOrder ( this, "-created_at")'>Created At</td>
							<td class="views" style="width: 100px;" onclick='changeMediaSortOrder (this, "-views")'><span>Views</span></td>
							<td class="views color2" onclick='changeMediaSortOrder (this, "-plays")'><span>Plays</span></td>
							<td class="type" style="width: 60px;">Contribs</td>
							<td class="type" style="width: 60px;">Entries</td>
							<td class="type" >User IP</td>
							<td class="views" onclick='changeMediaSortOrder (this, "-ip1_count") '><span>IP count</span></td>
						</tr>
					</thead>
					<tbody id="media_content">
						<?php echo $media_content ?>
					</tbody>
				</table>
		</div><!-- end middle-->
		<div class="clearfix">
			<ul class="pager" id="media_pagerB">
				<?php echo $htmlPager ?>
			</ul>
		</div>
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->


<?php 
return;
} else  { // not first time 
	$output = array(
		".currentPage" => $page,
		".maxPage" => $lastPage,
		".objectsInPage" => count($widget_log_list),
		".totalObjects" => $numResults,
		"media_content" => $media_content,
		"media_pager" => $htmlPager,
		"media_pagerB" => $htmlPager
		);
	
	echo json_encode($output);
}
?>
