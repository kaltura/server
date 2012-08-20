<?php
$service_url = requestUtils::getRequestHost();
?>
<style>
#kmcHeader { height:36px;}
#kmcHeader img { width:162px; height: 32px; }
   body { background-color:#272929 !important; background-image:none !important;}
   div.loginDesc { text-align:center; font-size:16px; font-weight:bold; color:white;}
div#varpartnerlist { height:auto; color:#000; padding:3px; padding-bottom:50px; font-size:12px; background: #f7f7f7 url(<?php echo $service_url; ?>/api_v3/system/images/bg.png) repeat-x;}
div#varpartnerlist div.myaccount { font-weight:bold; margin:10px 0px 10px 0px; font-size:14px;}
div#varpartnerlist h1 { color: #000000; }
h1 { color: #ffffff; }
a { color: #0099FF; text-decoration:underline;}
table { margin-left:10px; width:80%; border: solid 1px #CFD7D7; border-collapse:collapse;}
 table caption { padding: 25px 0 10px; text-align:left;}
 table caption h3 { margin-left:0px; }
 table th { font-size:14px;
 padding: 5px 10px 5px 5px;
 border: solid 1px #CFD7D7;
 background: #F1F1F1 url(<?php echo $service_url; ?>/api_v3/system/images/bg_th.png) repeat-x; color:#29464E; text-align:left; cursor:help;}
 table td { padding:5px; font-size:11px; background:#fff; border:1px solid #CFD7D7; }
  table tr.even td { background:#f0f0f0;}
h3.small { font-size:12px; width:100%; text-align:right; }
h3.other { font-size:14px; padding-top:5px; padding-left:10px; padding-bottom:2px; width:100%; text-align:left; }
div.login { background-color:#9FCBFF; }

#kmcSubMenu { margin: 1px 0 27px; display:table; zoom:1;}
 #kmcSubMenu li { float:left; display:inline; list-style: none inside none; padding-left:9px; height:19px;}
  #kmcSubMenu li a, #kmcSubMenu li a:active, #kmcSubMenu li a:visited { height:19px; padding: 1px 12px 0 4px; display:block; font: bold 12px arial,sans-serif; color:#666; text-decoration:none;}
  #kmcSubMenu li a:hover { color:#000;}
#kmcSubMenu li.active { margin-top:1px; background:url(<?php echo $service_url; ?>/api_v3/system/images/jellybean.png) 0 0 no-repeat;}
#kmcSubMenu li.active a { font-weights:normal; color:#fff; background:url(<?php echo $service_url; ?>/api_v3/system/images/jellybean.png) right 0 no-repeat;}

</style>
	<div id="kmcHeader">
     <img src="<?php echo $service_url; ?>/lib/images/kmc/varpages_logo.png" alt="Kaltura Management Console" />
     <ul>
      <li><h1><span>Publisher Management Console</span></h1></li>
      </ul>
     <div>
      <span>Hi <?php echo @$_GET['screen_name'] ?> | </span>
      <a href="javascript:logout()">Logout</a>
	 </div>

	</div><!-- kmcHeader -->     
<div id="varpartnerlist">
	<ul id="kmcSubMenu">
	<li class="active">
		<a title="list publishers" href="#">List Publishers</a>
	</li>
	</ul>
	<h3 class="small"><a href="<?php echo $varKmcUrl; ?>" target="kmc_<?php echo $me->getId();?>">Login to your KMC account</a></h3>
	<h3 class="other">Click 'Login' to open publisher's KMC in a new window</h3>
	<div id="partnerList">
		<table>
			<tr><th>Publisher Name</th><th>ID</th><th>Login</th></tr>
<?php
	$i = 1;
	foreach($partners as $partnerId => $arr)
	{
		if(!($i%2)) $tr_class = 'class="even"';
		else $tr_class = '';
?>
			<tr <?php echo $tr_class; ?>>
				<td class="name"><?php echo $arr['name']; ?></td>
				<td><?php echo $partnerId; ?></td>
				<td><a href="<?php echo $arr['kmcLink']; ?>" target="kmc_<?php echo $partnerId;?>">kmc login</a></td>
			</tr>
<?php
		$i++;
	}
?>
		</table>
	</div>
</div>
<script>
function logout()
{
	path = '/';
	deleteCookie ( "varpid" , path );
	deleteCookie ( "varsubpid" , path );
	deleteCookie ( "varuid" , path );
	deleteCookie ( "vplks" , path );
	// Codes by Quackit.com
	location = "<?php echo $service_url; ?>/index.php/kmc/varlogin";

}
$(function(){
	$('body').css('background-color', '#9FCBFF');
});
</script>