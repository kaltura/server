<?php
function prop ( $partner , $prop_name  )
{
	$stats = $partner->getPartnerStats();
	if ( $stats )
		return call_user_func( array ( $stats , "get" . $prop_name ));
	else
		return "";
}

function age ( $partner )
{
	$created_at = $partner->getCreatedAt(null);
	$str = kString::formatDate	( $created_at );
	return $str;
}

function partnerType ( $partner )
{
	$desc = $partner->getDescription();
	if ( stripos ( $desc , "wiki" ) !== FALSE ) return "WI";
	if ( ( stripos ( $desc , "wordpress" ) !== FALSE ) || ( $desc === "0" ) ) return "WP";
	return "?";
}

function printPartnerTable ( $partner_list )
{
	$str = "<tr style='color:white; background-color:gray;' ><td style='width:50px'>id</td>" .
		"<td style='width:200px;'>name</td>" .
		"<td>type</td>".		
		"<td>url</td>".
		"<td>age</td>".
		"<td>views</td>".
		"<td>plays</td>".
		"<td>entries</td></tr>";
	$i = 0;
	foreach ( $partner_list as $p ) 
	{
		$i++;
		$url = $p->getUrl1();
		$str .= "<tr style='background-color: " . ( $i % 2 ? "lightgray" : "lightblue" ) . "'>"  .
			"<td>" . $p->getId() . "</td>" .
			"<td>" . substr ( $p->getPartnerName() , 0 , 50 ) . "</td>". 
			"<td>" . partnerType ( $p ) . "</td>" .
//			"<td>" .$p->getAdminName() . "</td>" .
			"<td>" . "<a href='$url'>$url</a>" . "</td>" .
			"<td>". age ( $p ) . "</td>" .
			"<td>" . prop ( $p , "views" ) . "</td>" .
			"<td>" . prop ( $p , "plays" ) . "</td>" .
			"<td>" . prop ( $p , "entries" ) . "</td>" .
			"</tr>";
	}
	return $str;		
}
?>
<div >
<table style="font-weight: regular; font-size:18px; font-family:verdana">
<tr>
	<td>
		<table id='partners' cellpadding='10px'>
			<tr>
				<td>
<span style="float:right;" >Total count [<?php echo  $partner_count ?>]</span>				
New Partners<br>				
<table id='newest' border='1' cellpadding='3' cellspacing='1' _style='border-style:solid; '>
	<tr style='background-color:gray; color:white;'>
		<td>id</td>
		<td _style='width:200px;'>name</td>
		<td>type</td>				
		<td>url</td>		
<!--  <td>admin name</td>  -->		
		<td>admin email</td>
		<td _style='width:120px' >age</td>
	</tr>
<?php $i=0; foreach ( $newest_partners as $p ) { $i++ ; $url = $p->getUrl1() ; ?>
	
	<tr style='background-color: <?php echo  ( $i % 2 ? "lightgray" : "lightblue" )?>'>
		<td><?php echo  $p->getId() ?></td>
		<td><?php echo  substr ($p->getPartnerName() , 0 , 50 ) ?></td>
 		<td><?php echo  partnerType ( $p ) ?></td></td> 
<!--  <td><?php echo  $p->getAdminName() ?></td>  -->		
		<td><a href='<?php echo  $url ?>'><?php echo  $url ?></a></td>
		<td><?php echo  $p->getAdminEmail() ?></td>
		<td><?php echo  age ( $p ) ?></td>
	</tr>
<?php } ?>	
</table>
				</td>
			</tr>
			<tr>
				<td>
Partners with most views<br>				
<table id='most_views' border='1' _style='border-style:solid; '>
	<?php echo  printPartnerTable ( $most_views ) ;?>
</table>
				</td>
			</tr>
			<tr>
				<td>
Partners with most entries<br>				
<table id='most_entries' border='1' _style='border-style:solid; '>
	<?php echo  printPartnerTable ( $most_entries ) ;?>
</table>
				</td>
			</tr>			
						
		</table>
	</td>
	<td>
		<table id='system'>
		</table>
	</td>
</tr>
</table>
<script>
function updatePage()
{
//	alert ( "updatePage" );
	window.location = "./showtime";
}

window.setInterval("updatePage()" , 60000 ); 
</script>
</div>