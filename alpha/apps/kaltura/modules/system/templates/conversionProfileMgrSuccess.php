<?php
?>
<table cellpadding="3px" border="0px">
	<tr>
	<td style="vertical-align: top;">
<div style="font-family: calibri; margin:2px; width:100%; ">
	<form action="">
<table style=''>
	<tr><td colspan=2>ConversionParams filter</td></tr>
	<tr><td>id:</td><td><input name="filter__eq_id" value="<?php echo  $filter->get ("_eq_id") ?>" size=7></td></tr>
	<tr><td>partnerId:</td><td><input name="filter__eq_partner_id" value="<?php echo  $filter->get ("_eq_partner_id") ?>" size=7></td></tr>
	<tr><td>conversionType:</td><td><input name="filter__eq_profile_type" value="<?php echo  $filter->get ("_eq_profile_type") ?>" size=7></td></tr>
	<tr><td colspan=2><input type=submit name="go" value="go"></td></tr>
</table>
	</form>

<div style="width: 260px;height:500px; overflow:auto;">
<table cellpadding="3px" style="width:250px; padding:3px; margin:3px;">
	<tr style="background-color:#E2E2E2" >
		<td>id</td><td>partnerId</td><td>e</td><td>profileType</td>
	</tr>
<?php

$props = array ( "id" , "partnerId" , "enabled" , "profileType" );
$i=0;
foreach ( $list as $conversion_profile )
{	if ( $conversion_profile->getEnabled() == 0 )		$sty = "background-color: #CCC";			else		$sty = "background-color:" . ( $i%2  ? "#66FF99" : "#66FF99" ) . ";";	

	if( isset ( $conversion_profile->selected )) 
		$sty = "background-color: #33BBFF; cursor:pointer";
	echo "<tr onclick='openProfile(\"" . $conversion_profile->getId() . "\");' style='$sty'>" . 
		conversionHelper::propList( $conversion_profile , $props ) .
		"</td>";
	
	$i++;	
}
 ?>
 </table>
</div>

<button onclick="return createNew()">New</button>

</div>
</td>
<td style="width:100%; height:100%">
<iframe id="ifrm" style="border:1px; width:100%; height:800px;" src="">
</iframe>
</td>
</tr>
</table>



<script>
function openProfile ( id )
{
	var elem = document.getElementById( "ifrm" );
	var url = "<?php echo  url_for ( "/system" ) . "/conversionProfile?convprofile_id=" ?>" + id;
//	alert ( url );
	elem.src =  url;
}

function createNew()
{
	openProfile ( -1 );
}
var wrap = document.getElementById("wrap");
wrap.setAttribute( "style" , null );
</script>