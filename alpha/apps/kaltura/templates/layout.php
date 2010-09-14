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
		<div id="header" class="clearfix">
			<div class="wrapper clearfix">
				<a href="/index.php" class="logo"></a>
				<ul class="userMenu clearfix">	
					<li><a href='' onclick='onClickNavBarTour(); return false;'>Take a Tour</a></li>
					<?php $screenname = $sf_user->getAttribute('screenname');
							if($sf_user->isAuthenticated()) echo "<li class='bordered'><a href='' onclick='onClickNavBarSignOut(); return false;'>Sign out</a></li><li class='bordered user'>Hello,&nbsp;&nbsp;<a href='' onclick='onClickUserScreenName(this); return false;'>".$screenname."</a></li>";
									else echo "<li class='bordered' id='layout_signin'><a href='' onclick='onClickNavBarSignIn(); return false;'>Sign In</a></li>"?>
						<?php if(!$sf_user->isAuthenticated()) echo "<li class='bordered'><a href='' id='layout_signup' onclick='onClickNavBarRegister(); return false;'>Sign Up</a></li>" ?>
				</ul>
				<ul class="menu1">
					<li onclick='onClickNavBarBrowse()' <?php if( $sf_context->getModuleName() == 'browse' || $sf_context->getModuleName() == 'home' ) echo 'class="active"' ?> >Browse</li>
					<li onclick='onClickNavBarMyKaltura()' <?php if ( $sf_context->getModuleName() == 'mykaltura'  && $sf_user->isAuthenticated() && ( ( isset( $user ) && $sf_user->getAttribute('id') == $user->getId() ) || ( $sf_request->getParameter('user_id') == $sf_user->getAttribute('id')) )  ) echo 'class="active"' ?> >My Kaltura</li>
			
				</ul>
				<button class="btn4 btn4_white startKaltura" onclick="onClickNavBarCreate()">Start a Kaltura</button>
				<div class="search">
						<div class="left" ></div>
						<input type="text" id="navBarSearchInput" onkeydown="onKeyPressSearchInput(event)" onfocus="onFocusSearchInput()" />
						<div class="right" onclick="onClickNavBarSearch()"></div>
				</div><!-- end search-->
			</div><!-- end wrapper-->
		</div><!-- end header-->
		<?php echo $sf_content ?>
		<div id="clearfooter"></div>
	</div><!-- end wrap-->
	
	<div id="footer">
		<div class="content">
			<ul>
				<li><a href="/index.php/static/about">About Us</a></li>
				<li><a href="/index.php/static/developers">Developers</a></li>
				<li><a href="/index.php/forum">Support Forums</a></li>
				<li><a href="/index.php/static/jobs">Jobs</a></li>
				<li class="last"><a href="/index.php/static/contactus">Contact Us</a></li>
			</ul>
			<p>
				Copyright © 2007 Kaltura Inc. All Rights Reserved. Designated trademarks and brands are the property of their respective owners.
				<br/>
				Use of this web site constitutes acceptance of the
				<a href="/index.php/static/tandc">Terms of Use</a> and <a href="/index.php/static/privacy">Privacy Policy</a>
			</p>
		</div><!-- end content-->
	</div><!-- end footer-->
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>

<script type="text/javascript">
_uacct = "UA-2078931-1";
urchinTracker();
</script>	

</body>
</html>