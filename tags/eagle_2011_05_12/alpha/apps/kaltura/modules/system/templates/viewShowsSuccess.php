<?php

require_once ( "mySmartPagerRenderer.class.php" );

function addRow($kshow , $allowactions, $odd)
{
	$id = $kshow['id'];
	$link = '/index.php/browse?kshow_id='.$id;
	
	$s = '<tr '.($odd ? '' : 'class="even"').'>'.
	 	'<td class="imgHolder"><a href="'.$link.'"><img src="'.$kshow['image'].'" alt="Thumbnail" /></a></td>'.
	 	'<td class="info"><a href="'.$link.'">'.$kshow['name'].'</a><br/>'.$kshow['description'].'</td>'.
	 	'<td>'.$kshow['createdAt'].'</td>'.
	 	'<td>'.$kshow['updatedAt'].'</td>'.
		'<td>'.$kshow['roughcuts'].'</td>'.	 	
	 	'<td>'.$kshow['entries'].'</td>'.
	 	'<td>'.$kshow['contributors'].'</td>'.
	 	'<td>'.$kshow['comments'].'</td>'.
	 	'<td>'.$kshow['views'].'</td>'.
	 	'<td><div class="entry_rating" title="'.$kshow['rank'].'"><div style="width:'.($kshow['rank'] * 20).'%"></div></div></td>'.
	 	( $allowactions ? '<td class="action"><span class="btn" title="Customize" onclick="onClickCustomize('.$id.')"></span><span class="btn" title="Delete" onclick="onClickDelete('.$id.')" >Delete</span></td>' : '' ).
	 '</tr>';
	 
	return $s;
}

function firstPage($text, $pagerHtml, $producer_id, $actionTD, $kaltura_part_of_flag, $screenname, $partner_id)
{
	$KSHOW_SORT_MOST_VIEWED = kshow::KSHOW_SORT_MOST_VIEWED;  
	$KSHOW_SORT_MOST_RECENT = kshow::KSHOW_SORT_MOST_RECENT;  
	$KSHOW_SORT_MOST_ENTRIES = kshow::KSHOW_SORT_MOST_ENTRIES;
	$KSHOW_SORT_NAME = kshow::KSHOW_SORT_NAME;
	$KSHOW_SORT_RANK = kshow::KSHOW_SORT_RANK;
	$KSHOW_SORT_MOST_COMMENTS = kshow::KSHOW_SORT_MOST_COMMENTS;
	$KSHOW_SORT_MOST_UPDATED = kshow::KSHOW_SORT_MOST_UPDATED;
	$KSHOW_SORT_MOST_CONTRIBUTORS = kshow::KSHOW_SORT_MOST_CONTRIBUTORS;
	
	$options = dashboardUtils::partnerOptions ( $partner_id );
	
echo <<<EOT
<script type="text/javascript">


var producer_id = 0;
var kaltura_part_of_flag = 0;

jQuery(document).ready(function(){
mediaSortOrder = $KSHOW_SORT_MOST_VIEWED;
var defaultMediaPageSize = 10;
mediaPager = new ObjectPager('media', defaultMediaPageSize, requestMedia);
updatePagerAndRebind ( "media_pager" , null , requestMediaPage );

}); // end document ready


</script>
	<div class="mykaltura_viewAll">
		<div class="content">
			<div class="top">
				<div class="clearfix" style="margin:10px 0;">
					<ul class="pager" id="media_pager" style="float:right; margin:0;">
						$pagerHtml
					</ul>
					<select onchange="partnerSelect(this)" id="partner_id" style="float:left;">
						$options
					</select>
				</div>
			</div><!-- end top-->
			<div class="middle clearfix">	
					<table cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<td class="resource"></td>
								<td class="info" onclick='changeMediaSortOrder(this, $KSHOW_SORT_NAME)'><span>Kaltura Name</span></td>
								<td class="date" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_RECENT)'><span>Created</span></td>
								<td class="date" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_UPDATED)'><span>Updated</span></td>
								<td class="date" style="width: 25px">RC</td>
								<td class="entries" style="width: 40px" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_ENTRIES)'><span>Entries</span></td>
								<td class="date" style="width: 50px" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_CONTRIBUTORS)'><span>C'tors</span></td>
								<td class="date" style="width: 60px" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_COMMENTS)'><span>Comments</span></td>
								<td class="views color2" onclick='changeMediaSortOrder(this, $KSHOW_SORT_MOST_VIEWED)'><span>Views</span></td>
								<td class="rating" style="width: 60px" onclick='changeMediaSortOrder(this, $KSHOW_SORT_RANK)'><span>Rating</span></td>
								$actionTD
							</tr>
						</thead>
						<tbody id="media_content">
							$text
						</tbody>
					</table>
				
			</div><!-- end middle-->
		</div><!-- end content-->
		<div class="bgB"></div>
	</div><!-- end media-->
EOT;
}


if( $allowactions ) $actionTD = '<td class="action" >Action</td>'; else $actionTD = '';

$text = '';
$i = 0;
foreach($kshowsData as $kshow)
{
	$text .= addRow($kshow , $allowactions, $i);
	$i = 1 - $i;
}
	
$htmlPager = mySmartPagerRenderer::createHtmlPager( $lastPage , $page  );
			
if ($firstTime)
	firstPage($text, $htmlPager, $producer_id, $actionTD, $kaltura_part_of_flag, $screenname , $partner_id );
else {
	$output = array(
		".currentPage" => $page,
		".maxPage" => $lastPage,
		".objectsInPage" => count($kshowsData),
		".totalObjects" => $numResults,
		"media_content" => $text,
		"media_pager" => $htmlPager
		);
	
	echo json_encode($output);
}		

?>