<?php

function setHint ( $hint )
{
	global $g_hint ;
	$g_hint = $hint;
}
function prop ( $obj , $prop , $max_len = null )
{
	global $g_hint ;
	$method_name = "get{$prop}";
	$res = call_user_func( array ( $obj , $method_name ));
	if ( $max_len )
		$res =  substr ( $res , 0 , $max_len );
	
	if ( $prop == "id")
	{
		$res = "<a href='javascript:openPartner(\"{$prop}\")'>{$res}</a>";
	}
	$res = str_replace ( $g_hint , "<span  style='font-size:20px; font-weight: bold; color:green;'>$g_hint</span>" , $res );
	return "<td>$res</td>";
}	

setHint ( $hint );
?>
<script>
function openPartner ( pid )
{
	url = '<?php echo url_for ( "/system" )  ?>' + "/partners?partner_id=" + pid + "&go=go";
	var handle = window.open ( url , "partner" ); 
}
</script>
<div style='font-family:verdana; font-size:12px;'>
<form method="get">
	keyword: <input type="text" name="hint" value="<?php echo $hint ?>">
	<input type="submit" name="search" value="search"/>
	Use the '%' character as a wild-char between words
</form>
<table border=1 cellpadding=2 cellspacing=0>
	<tr>
		<td>id</td><td>name</td><td>description</td><td>admin email</td><td>created at</td><td>package</td>
	</tr>
<?php
foreach ( $partner_list as $p )
{
	echo "<tr>" .
		prop ( $p , "id" ). "</a>" .
		prop ( $p , "partnername" ).
		prop ( $p , "description" , 80 ).
		prop ( $p , "adminemail" ).
		prop ( $p , "createdat" ).
		prop ( $p , "partnerpackage" ).
		"</tr>";
}
 ?>
</table>
</div>
