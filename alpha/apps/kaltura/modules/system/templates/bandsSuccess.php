<?php

?>
<a href="/index.php/system/login?exit=true">logout</a> Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>

bands (<?php echo count ($band_list) ?>)<br>

<table cellpadding="5" cellspacing="2" style="font-family:arial;font-size:12px">

<?php 
$odd=true;
$i=1;
foreach ( $band_list as $band ) { 
	$band_id = $band->getIndexedCustomData1();
	$hash == md5($band_id."dont fuck with us");
	$odd = !$odd; 
	?>
<tr <?php echo ($odd ? "style='background-color:#eee'" : "" ); ?>>
	<td><?php echo $i ?></td> 
	<td><?php echo $band->getId() ; ?></td>
	<td><a href="javascript:goto ( 	<?php echo  "'" . $band_id . "' , 1 , '" . $hash . "'"  ?>	) >Kaltura's <?php echo  $band_id; ?></a></td>
	<td><a href="javascript:goto (	<?php echo  "'" . $band_id . "' , 2 , '" . $hash . "'"  ?>	)">Myspace's profile <?php echo  $band_id; ?></a></td>
	<td><a href="javascript:goto (	<?php echo  "'" . $band_id . "' , 3 , '" . $hash . "'"  ?>	 )">Myspace's message <?php echo  $band_id; ?></a></td>
	<td><?php echo $band->getKuser()->getScreenName() ; ?></td>
	<td><?php echo $band_id ?></td> 
<td><?php echo $band->getFormattedCreatedAt() ?></td></tr>

<?php 
$i++;
} ?>
</table>

<script>
function goto ( url , type , hash )
{
	kaltura_url = "http://www.kaltura.com/index.php/browse/bands?band_id=";
	myspace_profile = "http://profile.myspace.com/index.cfm?fuseaction=user.viewprofile&friendID=";
	myspace_message = "http://messaging.myspace.com/index.cfm?fuseaction=mail.message&friendID=";
	
	if( type == 1 )
		fixed_url =kaltura_url;
	else if ( type ==2 )
		fixed_url = myspace_profile;
	else 
		fixed_url = myspace_message;
		
	fixed_url += url;

	if( type == 1 )
		fixed_url += "I" + hash ;
			 
	handle = window.open(  fixed_url );//, null  , "" );
	handle.focus();
}
</script>