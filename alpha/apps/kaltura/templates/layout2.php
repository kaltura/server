<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<?php if( isset( $facebook_tags )) echo $facebook_tags; else { include_title(); include_metas(); } ?>
	<link rel="stylesheet" type="text/css" media="screen" href="/css/layout.css" />
	<script type="text/javascript">
		var ENTRY_MEDIA_TYPE_VIDEO = <?php echo entry::ENTRY_MEDIA_TYPE_VIDEO; ?>;
		var ENTRY_MEDIA_TYPE_IMAGE = <?php echo entry::ENTRY_MEDIA_TYPE_IMAGE; ?>;
		var ENTRY_MEDIA_TYPE_AUDIO = <?php echo entry::ENTRY_MEDIA_TYPE_AUDIO; ?>;
		var ENTRY_MEDIA_TYPE_TEXT = <?php echo entry::ENTRY_MEDIA_TYPE_TEXT; ?>;
		var ENTRY_MEDIA_TYPE_SHOW = <?php echo entry::ENTRY_MEDIA_TYPE_SHOW; ?>;
		
		var ENTRY_MEDIA_SOURCE_FILE = <?php echo entry::ENTRY_MEDIA_SOURCE_FILE; ?>;
		var ENTRY_MEDIA_SOURCE_FLICKR = <?php echo entry::ENTRY_MEDIA_SOURCE_FLICKR; ?>;
		var ENTRY_MEDIA_SOURCE_YOUTUBE = <?php echo entry::ENTRY_MEDIA_SOURCE_YOUTUBE; ?>;
		var ENTRY_MEDIA_SOURCE_URL = <?php echo entry::ENTRY_MEDIA_SOURCE_URL; ?>;
		var ENTRY_MEDIA_SOURCE_WEBCAM = <?php echo entry::ENTRY_MEDIA_SOURCE_WEBCAM; ?>;
		var ENTRY_MEDIA_SOURCE_MYSPACE = <?php echo entry::ENTRY_MEDIA_SOURCE_MYSPACE; ?>;
		var ENTRY_MEDIA_SOURCE_PHOTOBUCKET = <?php echo entry::ENTRY_MEDIA_SOURCE_PHOTOBUCKET; ?>;
		var ENTRY_MEDIA_SOURCE_NYPL = <?php echo entry::ENTRY_MEDIA_SOURCE_NYPL; ?>;
		var ENTRY_MEDIA_SOURCE_JAMENDO = <?php echo entry::ENTRY_MEDIA_SOURCE_JAMENDO; ?>;
		var ENTRY_MEDIA_SOURCE_CCMIXTER = <?php echo entry::ENTRY_MEDIA_SOURCE_CCMIXTER; ?>;
		var ENTRY_MEDIA_SOURCE_CURRENT = <?php echo entry::ENTRY_MEDIA_SOURCE_CURRENT; ?>;
		
		var COMMENT_TYPE_KSHOW = <?php echo comment::COMMENT_TYPE_KSHOW; ?>;
		var COMMENT_TYPE_DISCUSSION = <?php echo comment::COMMENT_TYPE_DISCUSSION; ?>;
		var COMMENT_TYPE_USER = <?php echo comment::COMMENT_TYPE_USER; ?>;
		var COMMENT_TYPE_SHOUTOUT = <?php echo comment::COMMENT_TYPE_SHOUTOUT; ?>;
		
		var KSHOW_PERMISSION_INVITE_ONLY = <?php echo kshow::KSHOW_PERMISSION_INVITE_ONLY; ?>;
		
		var KSHOWKUSER_VIEWER_USER = <?php echo KshowKuser::KSHOWKUSER_VIEWER_USER; ?>;
		var KSHOWKUSER_VIEWER_SUBSCRIBER = <?php echo KshowKuser::KSHOWKUSER_VIEWER_SUBSCRIBER; ?>;
		var KSHOWKUSER_VIEWER_PRODUCER = <?php echo KshowKuser::KSHOWKUSER_VIEWER_PRODUCER; ?>;
		
		<?php
		
		$modules = array('browse', 'contribute', 'edit', 'emailImport', 'kshowcss',
			'login', 'mail', 'mykaltura', 'produce', 'home', 'tour', 
			'search', 'sns', 'static', 'system', 'upload', 'forum' );
			
		foreach($modules as $module)
		{
			echo "var MODULE_".strtoupper($module)." = '".url_for("/$module")."';\n";
		}
		
		?>
	</script>
</head>
<body class="cls_body_b">
	<div id="utilsPlaceHolder"></div>
	<div id="SWFUpload" style='position:fixed;'></div>
	<div id="statusBar"><div>Status message</div></div>
	
	<div id="wrap">
		<div id="minHeight"></div> <!-- Safari hack -->
		<div id="header2">
			<a href="<?php echo url_for('/corp'); ?>" class="logo"></a>
			<div class="outerwrap">
				<div class="innerwrap sch_boxBorders">
					<ul class="userMenu">	
						<li class="bordered"><a href='<?php echo url_for('/corp'); ?>'>Home</a></li>
						<?php $screenname = $sf_user->getAttribute('screenname');
								if($sf_user->isAuthenticated())
								{
									echo "<li class='user bordered'>Hello,<a href='".url_for('/mykaltura')."/viewprofile?screenname=$screenname'>". substr( $screenname, 0, 13 )."</a></li>";
									echo "<li><a href='".url_for('/login/signout')."'>Sign out</a></li>";
								}
								else
								{
									echo "<li class='bordered'><a href='".url_for('/login/register')."' id='layout_signup'>Sign Up</a></li>";
									echo "<li id='layout_signin'><a href='' onclick='onClickNavBarSignIn(); return false;'>Sign In</a></li>";
								}
						?>
					</ul>
					<button class="startKaltura" onclick="onClickNavBarCreate()"></button>
					<ul class="bottomLinks userMenu">
						<li class="bordered"><a href="<?php echo url_for('/edit')."?kshow_id=2"; ?>">Try out video editor</a></li>
						<li><a href="<?php echo url_for('/tour'); ?>">Take the tour</a></li>
					</ul><!-- end bottomLinks-->
					<div class="search"><div><input type="text" id="navBarSearchInput" value="Search Kaltura" /></div><b id="navBarSearcGo"></b></div><!-- end search-->
				</div><!-- end innerwrap-->
				<?php
				if (!@$disableGoogleAd)
				{
				?>
				<div class="ad">
					<script type="text/javascript">
					<!--
						google_ad_client = "pub-0385680126062541";
						google_ad_width = 468;
						google_ad_height = 60;
						google_ad_format = "468x60_as";
						google_ad_type = "text_image";
						google_ad_channel = "";
						google_color_border = "222222";
						google_color_bg = "262626";
						google_color_link = "A0BA68";
						google_color_text = "000000";
						google_color_url = "96E5FF";
						google_ui_features = "rc:6";
					//-->
					</script>
					<script type="text/javascript"
						src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script> 
				</div>
				<?php
				}
				else {
				?>
				<!--  <a style="width:405px; height:60px; margin:45px 0 0 255px; display:block; background: url(/images/press/logos/TCbanner.gif) no-repeat 0 0;" href="/index.php/static/news"></a>  -->
				<div style="height: 70px; line-height:70px; margin:45px 0 0 255px;">
					<a style="display: inline; margin-right: 40px;" href="http://mashable.com/2007/12/21/open-web-awards-winners/"><img src="http://www.kaltura.com/content/dynamic/mashableOW.gif" alt="" /></a>
					<a style="display: inline;" href="http://www.techcrunch.com/2007/09/18/kaltura-wins-spot-as-40th-company-at-techcrunch40/"><img src="http://www.kaltura.com/content/dynamic/techCrunchWinner.gif" alt="" /></a>
				</div>
				<?php
				}
				?>
			</div><!-- end outerwrap-->
		</div><!-- end header-->
		<?php echo $sf_content ?>
		<div id="clearfooter"></div>
	</div><!-- end wrap-->
	
	<div id="footer">
		<div class="content">
			<ul>
				<li><a href="/index.php/static/about">About Us</a></li>
				<li><a href="/index.php/static/news">News</a></li>
				<li><a href="/blog/index.php">Blog</a></li>
				<li><a href="/index.php/static/partners">Partners</a></li>
				<li><a href="/wiki/index.php">Developers Wiki</a></li>
				<li><a href="/index.php/forum">Support Forums</a></li>
				<li><a href="/index.php/static/jobs">Jobs</a></li>
				<li class="last"><a href="/index.php/static/contactus">Contact Us</a></li>
			</ul>
			<p>
				Copyright © 2008 Kaltura Inc. All Rights Reserved. Designated trademarks and brands are the property of their respective owners.
				<br/>
				Use of this web site constitutes acceptance of the <a href="/index.php/static/tandc">Terms of Use</a> and <a href="/index.php/static/privacy">Privacy Policy</a><br/>
				<br />This work is licensed under a 
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-Share Alike 3.0 Unported License</a>.
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="border:none;">
					<img alt="Creative Commons License" style="margin:0 0 -12px 10px;" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" />
				</a>
			</p>
		</div><!-- end content-->
	</div><!-- end footer-->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "UA-2078931-1";
urchinTracker();
</script>	

</body>
</html>