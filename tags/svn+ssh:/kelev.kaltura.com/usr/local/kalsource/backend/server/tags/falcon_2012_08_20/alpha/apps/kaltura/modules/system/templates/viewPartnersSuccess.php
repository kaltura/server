<?php
global $global_dual ;
$global_dual = $dual;
if ( $type==2 )
{
	$excel = true;
	while(FALSE !== ob_get_clean());
	$content_type ="application/vnd.ms-excel";
//	$content_type ="text/plain";
	// excel
	header ( "Content-type: $content_type");
}
else
{
	$excel = false;
}

		

function addRow($partner_stat, $even_row )
{
	$email = @$partner_stat["email"];
		
	$description = @$partner_stat["description"];

	$url = @$partner_stat["url1"];
	if ( $url ) $url = "<a href='javascript:newWindow(\"$url\");'>$url</a>";

	$widget_count = @$partner_stat["widgets"] > 0;
	
	$s = 
		'<tr ' . ( $even_row ? 'class="even" ' : '' ). '>'.
		'<td>' . ( $widget_count ? 
			'<a href="javascript:referer( \'' . $partner_stat["id"] . '\');">'.$partner_stat["id"].'</a>' : 
			$partner_stat["id"] ) . '</td>'.
	 	'<td style="text-align:left;">'.@$partner_stat["name"].'</td>'.
	 	'<td style="text-align:left;">'.$email.'</td>' . 		
	 	'<td style="text-align:left;">'.$url.'</td>' .
	 	'<td style="text-align:left;">'.$description.'</td>' .
		'<td>'.prop($partner_stat , "categories" ). '</td>'.
		'<td>'.prop($partner_stat , "created" ). '</td>'.
 		'<td>'.prop($partner_stat , "views",true )  .'</td>'.
		'<td>'.prop($partner_stat , "plays" , true ). '</td>'. 		
// 		'<td>'.prop($partner_stat , "kusers" , true ).'</td>'.
//	 	'<td>'.prop($partner_stat , "contribs" , true ). '</td>'.
	 	'<td>'.prop($partner_stat , "rcs" , true ). '</td>'.
	 	
	 	'<td>'.prop($partner_stat , "widgets" , true ). '</td>'.
	 	'<td>'.prop($partner_stat , "entries" , true ). '</td>'.
		'<td>'.@$partner_stat["videos"] . '</td>'.
	 	'<td>'.@$partner_stat["audios"] . '</td>'.		
	 	'<td>'.@$partner_stat["images"] . '</td>'.
	 	'<td>'.@$partner_stat["activeSite7"] . '</td>'.
	 	'<td>'.@$partner_stat["activeSite30"] . '</td>'.
	 	'<td>'.@$partner_stat["activeSite180"] . '</td>'.
	 	'<td>'.@$partner_stat["activePublisher7"] . '</td>'.
	 	'<td>'.@$partner_stat["activePublisher30"] . '</td>'.
	 	'<td>'.@$partner_stat["activePublisher180"] . '</td>'.
	 	'<td>'.@$partner_stat["bandwidth"] . '</td>'.
	 	'<td>'.@$partner_stat["bandwidth_gt"] . '</td>'.
//		'<td>'.@$partner_stat["ready_entries"] . '</td>'.
		'</tr>';
	 
	return $s;
}

function prop ( $arr , $prop_name , $dual_field = false )
{
	global $global_dual;
	$val = @$arr[$prop_name];
	if ( $dual_field && $global_dual ) $val .= "|" . @$arr[$prop_name."2"];
	return $val; 
}

?>
<?php if ( ! $excel ) { ?>
<script type="text/javascript">

function newWindow ( url )
{
	h = window.open ( url );
	return ;
}


function referer ( partner_id )
{
	r = window.open ( "./referer?partner_id=" + partner_id  , "referer" );
	r.focus();
	return ;
}
// TODO - replace ! - use form
function filterPartners ( type )
{
	pf = document.getElementById( "partner_filter" );
	pt = document.getElementById( "filter_type" );
	fd = document.getElementById( "from_date" );
	td = document.getElementById( "to_date" );
	d = document.getElementById( "days" );
	nf = document.getElementById( "new_first" );
	pb = document.getElementById( "partners_between" );
	page = document.getElementById( "page" );
	
	location = "./viewPartners?partner_filter=" + pf.value + "&filter_type=" + pt.value + "&type=" + type + 
		"&from_date=" + fd.value + "&to_date=" + td.value + "&days=" + d.value + "&new_first=" + nf.checked + "&partners_between=" + pb.checked + 
		"&page=" + page.value;
}

function updateSelect( select_elem )
{
	val = select_elem.value;
	if ( val == 'filter' ) filter='';
	else filter=val;
	
	pf = document.getElementById( "partner_filter" );
	pf.value = filter;
}

function openGroups()
{
	r = window.open ( "./viewPartnersEditData" , "editData" );
	r.focus();
	return ;
 
}
</script>

<div class="mykaltura_viewAll mykaltura_media" style="width: 98%;">
	<div>
		<span style="float:right;">
			<a href="javascript:openGroups()">Groups</a> 
			Updated at <?php echo  $updated_at ?> &nbsp;&nbsp;&nbsp;&nbsp;
			<a  href='#' onclick='filterPartners(2);' >Save as Excel</a>
		</span>
		
		New first <input type="checkbox" id="new_first" name="new_first" <?php echo $new_first ? "checked='checked'" : "" ?>>
		<span>Partners:</span>
		<select name='filter_type' id='filter_type' onkeyup='updateSelect( this )' onchange='updateSelect( this )'>
			<option value="">All</option>
<?php
foreach ( $partner_group_list as $group )
{
	echo "<option value='{$group->name}'>{$group->name}</option>";
}

?>			
			<option value="filter">Free filter</option>
		</select> 
		<input id="partner_filter" name="partner_filter" value="<?php echo  $partner_filter ?>" size="10"> 
		
		FROM (YYYY-MM-DD): <input id="from_date" name="from_date" type="text" size=10 value="<?php echo $from_date ?>" >
		TO (YYYY-MM-DD): <input id="to_date" name="to_date" type="text" size=10 value="<?php echo $to_date ?>" >
		Days: <input id="days" name="days" type="text" size=3 value="<?php echo $days ?>" >

		<button style='color:black' name="go" value="Go" onclick="filterPartners(1);">Go</button>
		<br>
		Page: <input id="page" name="page" type="text" style='width:20px' value="<?php echo $page ?>" >
		Partners between only: <input type="checkbox" name="partners_between" id="partners_between" <?php echo $partners_between ? "checked='checked'" : "" ?>>
<?php if ( count ( $partners_stat ) > 0 ) echo "&nbsp;&nbsp;Results: [" . count ( $partners_stat ) . "]";		?>

	</div>
	<div class="content">
		<div class="middle">	
<?php } ?>		
				<table cellspacing="0" cellpadding="0">
					<thead>
						<tr style="width:200px; text-align:center;" >
							<td colspan=7>Partner data</td> 
							<td colspan=4>General</td>
<?php // 							<td style="width:40px;" class="type" ><span>Kusers</span></td> ?>
<?php //							<td style="width:40px;" class="type" ><span>Contributors</span></td> ?>
							<td colspan=4>Entries</td>
							<td colspan=3>Active Site (views:plays)</td>
							<td colspan=3>Active Pub (cont:players)</td>
							
							<td colspan=2>Bandwidth (GB)</td>
						</tr>
											
						<tr style="width:200px; text-align:center;" >
							<td class="type" style="width: 40px;" ><span>Id</span></td>
							<td class="type" style="width:120px; text-align:center;" ><span>Name</span></td>
							<td class="type" style="width:120px; text-align:center;" ><span>Email</span></td>
							<td class="type" style="width:120px; text-align:center;" ><span>Url</span></td>
							<td class="type" style="width:200px; text-align:center;" ><span>Description</span></td>
							<td style="width:60px;" class="type" ><span>Categories</span></td>
							<td style="width:60px;" class="type" ><span>Created</span></td>
							<td style="width:50px;" class="type" ><span>Views</span></td>
							<td style="width:50px;" class="type" ><span>Plays</span></td>
<?php // 							<td style="width:40px;" class="type" ><span>Kusers</span></td> ?>
<?php //							<td style="width:40px;" class="type" ><span>Contributors</span></td> ?>
							<td style="width:30px;" class="type" >RC &gt;= 2</td>
							<td style="width:40px;" class="type" >Widgets</td>
							<td style="width:40px;" class="type" ><span>Entries</span></td>
							<td style="width:40px;" class="type" ><span>Videos</span></td>
							<td style="width:40px;" class="type" ><span>Audios</span></td>
							<td style="width:40px;" class="type" ><span>Images</span></td>
							<td style="width:40px;" class="type" ><span>7</span></td>							
							<td style="width:60px;" class="type" ><span>30</span></td>
							<td style="width:60px;" class="type" ><span>180</span></td>

							<td style="width:40px;" class="type" ><span>7</span></td>							
							<td style="width:60px;" class="type" ><span>30</span></td>
							<td style="width:60px;" class="type" ><span>180</span></td>

							<td style="width:80px;" class="type" ><span>For dates</span></td>
							<td style="width:80px;" class="type" ><span>Total</span></td>
						</tr>
					</thead>
					<tbody id="media_content">
<?php
$i=0;
$media_content = "";
foreach($partners_stat as $partner_stat)
{
	 $media_content .= addRow($partner_stat, ( $i % 2 == 0 ) );
	++$i;
	
	if ( $i % 10 == 0 )
	{
		echo $media_content;
		$media_content = "";
	}
}
echo $media_content; 
?>					

					</tbody>
				</table>
				
<?php if ( ! $excel ) { ?>				
		</div><!-- end middle-->
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->

<script type="text/javascript">

$$ = function(x) { return document.getElementById(x); }

jQuery.noConflict();
jQuery(document).ready(function(){
})
pt = jQuery ( "#filter_type" );
pt.attr ( 'value' , '<?php echo  $filter_type ?>' );
</script>

<?php } 
else { die; }
