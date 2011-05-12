<?php

require_once ( "mySmartPagerRenderer.class.php" );

function addRow($kuser, $even_row )
{
	
	$link = '/index.php/mykaltura/viewprofile?screenname=' .$kuser['screenname'];
	
	$s = '<tr ' . ( $even_row ? 'class="even" ' : '' ). '>'.
	 	'<td class="imgHolder"><a href="'.$link.'"><img src="'.$kuser['image'].'" alt="'.$kuser['screenname'].'" /></a></td>'.
	 	'<td class="info"><a href="'.$link.'">'.$kuser['fullname'].'</a></td>'.
	 	'<td><img src="/images/flags/'.strtolower($kuser['country']).'.gif"></td>'.
	 	'<td>'.$kuser['gender'].'</td>'.
	 	'<td>'.$kuser['createdAt'].'</td>'.
	 	'<td>'.$kuser['views'].'</td>'.
	 	'<td>'.$kuser['fans'].'</td>'.
	 	'<td>'.$kuser['shows'].'</td>'.
	 	'<td>'.$kuser['roughcuts'].'</td>'.
	 	'<td>'.$kuser['entries'].'</td>'.
	 	'<td class="action"><span class="btn" title="Delete" onclick="onClickDeleteUser('.$kuser['id'].')"></span>Delete</td>'.
	 '</tr>';
	 
	return $s;
}



function firstPage($text, $pagerHtml, $user_id , $partner_id)
{
	
	$KUSER_SORT_MOST_VIEWED = kuser::KUSER_SORT_MOST_VIEWED;
	$KUSER_SORT_MOST_RECENT = kuser::KUSER_SORT_MOST_RECENT;  
	$KUSER_SORT_NAME = kuser::KUSER_SORT_NAME;
	$KUSER_SORT_AGE = kuser::KUSER_SORT_AGE;
	$KUSER_SORT_COUNTRY = kuser::KUSER_SORT_COUNTRY;
	$KUSER_SORT_CITY = kuser::KUSER_SORT_CITY;
	$KUSER_SORT_GENDER = kuser::KUSER_SORT_GENDER;
	$KUSER_SORT_MOST_FANS = kuser::KUSER_SORT_MOST_FANS;
	$KUSER_SORT_MOST_ENTRIES = kuser::KUSER_SORT_MOST_ENTRIES;
	$KUSER_SORT_PRODUCED_KSHOWS = kuser::KUSER_SORT_PRODUCED_KSHOWS;
	
	$options = dashboardUtils::partnerOptions ( $partner_id );
	
echo <<<EOT
<script type="text/javascript">

jQuery(document).ready(function(){
	mediaSortOrder = $KUSER_SORT_MOST_VIEWED;
	var defaultMediaPageSize = 10;
	mediaPager = new ObjectPager('media', defaultMediaPageSize, requestMediaPeople );
	updatePagerAndRebind ( "media_pager" , null , requestMediaPagePeople );
	updatePagerAndRebind ( "media_pagerB" , null , requestMediaPagePeople );

}); // end document ready

</script>
	<div class="mykaltura_viewAll mykaltura_media">
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
			<div class="middle">	
					<table cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<td class="resource"></td>
								<td class="info" onclick='changeMediaSortOrder(this, $KUSER_SORT_NAME)'><span>Screen Name</span></td>
								<td class="type" onclick='changeMediaSortOrder(this, $KUSER_SORT_COUNTRY)'><span>Country</span></td>
								<td class="rating" onclick='changeMediaSortOrder(this, $KUSER_SORT_GENDER)'><span>Gender</span></td>
								<td class="date" onclick='changeMediaSortOrder(this, $KUSER_SORT_MOST_RECENT)'><span>Created</span></td>
								<td class="views color2" onclick='changeMediaSortOrder(this, $KUSER_SORT_MOST_VIEWED)'><span>Views</span></td>
								<td class="views" onclick='changeMediaSortOrder(this, $KUSER_SORT_MOST_FANS)'><span>Fans</span></td>
								<td class="views" onclick='changeMediaSortOrder(this, $KUSER_SORT_PRODUCED_KSHOWS)'><span>Shows</span></td>
								<td class="date" style="width: 25px; cursor:default;">RC</td>
								<td class="views" onclick='changeMediaSortOrder(this, $KUSER_SORT_MOST_ENTRIES)'><span>Entries</span></td>
								<td class="action" >Action</td>
							</tr>
						</thead>
						<tbody id="media_content">
							$text
						</tbody>
					</table>
			</div><!-- end middle-->
			<div class="clearfix">
				<ul class="pager" id="media_pagerB">
					$pagerHtml
				</ul>
			</div>
		</div><!-- end content-->
		<div class="bgB"></div>
	</div><!-- end media-->
EOT;
}


$text = '';
$i=0;
foreach($kusersData as $kuser)
{
	$text .= addRow($kuser, ( $i % 2 == 0 ) );
	++$i;
}
	
$htmlPager = mySmartPagerRenderer::createHtmlPager( $lastPage , $page );

if ( !isset($user_id) ) $user_id=null;

if ($firstTime)
	firstPage($text, $htmlPager, $user_id , $partner_id );
else {
	$output = array(
		".currentPage" => $page,
		".maxPage" => $lastPage,
		".objectsInPage" => count($kusersData),
		".totalObjects" => $numResults,
		"media_content" => $text,
		"media_pager" => $htmlPager,
		"media_pagerB" => $htmlPager
		);
	
	echo json_encode($output);
}		

?>