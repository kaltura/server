<?php
require_once("../../../bootstrap.php");
require_once("../config.php");
require_once("../lib/KalturaClient.php");

$config = new KalturaConfiguration(PARTNER_ID);
$config->serviceUrl = SERVER_URL;
$client = new KalturaClient($config);

// for better performance, you might want to load other tabs using ajax when the user clicks on the tab
$playlistEntries1 = $client->playlist->execute("644igevrzs");
$playlistEntries2 = $client->playlist->execute("8j12w5m41s");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Kaltura Demo Playlists</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="css/kaltura.css" />
    <script type="text/javascript" src="js/swfobject.js"></script>
    <script type="text/javascript" src="js/kaltura_player_controller.js"></script>
    <script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.mousewheel.pack.js"></script>
    <script type="text/javascript" src="js/jquery.scrollable-1.0.1.pack.js"></script>
    <script type="text/javascript" src="js/functions.js"></script>
</head>
<body>
    <div id="kaltura">
        <ul id="videoBoxMenu" class="clearfix">
            <li class="active"><a id="tab1">Tab 1</a></li>
            <li><a id="tab2">Tab 2</a></li>
            <li><a id="tab3">Tab 3</a></li>
            <li><a id="tab4">Tab 4</a></li>
            <li class="last"><a id="tab5">Tab 5</a></li>
        </ul>
        <!-- end #videoBoxMenu-->
        <div id="videoboxContent" class="clearfix">
            <div id="kplayer">
                <div style="" class="kaltura_wrapper" id="kaltura_wrapper_alwayson">
                </div>
                <script type="text/javascript">
                 var kaltura_swf = new SWFObject("<?php echo SERVER_URL; ?>/kwidget/wid/_1/entry_id/jwfb9cpir4/ui_conf_id/48411", "kaltura-static-playlist", "389", "350", "9", "#ffffff");
                 kaltura_swf.addParam("wmode", "opaque");
                 <?php if (count($playlistEntries1)): ?>
                 	kaltura_swf.addParam("flashVars", "entryId=<?php echo $playlistEntries1[0]->id; ?>");
                 <?php endif; ?>
                 kaltura_swf.addParam("allowScriptAccess", "always");
                 kaltura_swf.addParam("allowFullScreen", "true");
                 kaltura_swf.addParam("allowNetworking", "all");
                 kaltura_swf.write("kaltura_wrapper_alwayson");
                </script>
			</div>
			<!-- Kaltura player goes here -->
			<div class="mediaContent">


				<div class="row row1 clearfix" id="tab1_row1">
					<span class="videoBoxTitle">Demo Playlist 1</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						<ul id="thumbs_vc_f" class="clearfix">
							<?php foreach($playlistEntries1 as $entry): ?>
							<li id="<?php echo $entry->id; ?>">
								<div>
									<img src="<?php echo $entry->thumbnailUrl; ?>" alt="" />
									<b></b>
								</div>
								<strong><?php echo $entry->name; ?></strong>
								<span><?php echo $entry->name; ?></span>
								<span><?php echo $entry->createdAt; ?></span>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<!-- end scrollable-->
				</div><!-- end row1-->


				<div class="row row2 clearfix" id="tab1_row2">
					<span class="videoBoxTitle">Demo Playlist 2</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						<ul id="thumbs_vc_f" class="clearfix">
							<?php foreach($playlistEntries2 as $entry): ?>
							<li id="<?php echo $entry->id; ?>">
								<div>
									<img src="<?php echo $entry->thumbnailUrl; ?>" alt="" />
									<b></b>
								</div>
								<strong><?php echo $entry->name; ?></strong>
								<span><?php echo $entry->name; ?></span>
								<span><?php echo $entry->createdAt; ?></span>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<!-- end scrollable-->
				</div>
				<!-- end row2-->


                
				<div class="row row1 clearfix" id="tab2_row1">
					<span class="videoBoxTitle">Demo Playlist 3</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						<ul id="thumbs_vc_f" class="clearfix">
							<?php foreach($playlistEntries1 as $entry): ?>
							<li id="<?php echo $entry->id; ?>">
								<div>
									<img src="<?php echo $entry->thumbnailUrl; ?>" alt="" />
									<b></b>
								</div>
								<strong><?php echo $entry->name; ?></strong>
								<span><?php echo $entry->name; ?></span>
								<span><?php echo $entry->createdAt; ?></span>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<!-- end scrollable-->
				</div><!-- end row1-->


				<div class="row row2 clearfix" id="tab2_row2">
					<span class="videoBoxTitle">Demo Playlist 4</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						<ul id="thumbs_vc_f" class="clearfix">
							<?php foreach($playlistEntries2 as $entry): ?>
							<li id="<?php echo $entry->id; ?>">
								<div>
									<img src="<?php echo $entry->thumbnailUrl; ?>" alt="" />
									<b></b>
								</div>
								<strong><?php echo $entry->name; ?></strong>
								<span><?php echo $entry->name; ?></span>
								<span><?php echo $entry->createdAt; ?></span>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<!-- end scrollable-->
				</div>
				<!-- end row2-->

				<div class="row row1 clearfix" id="tab3_row1">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div><!-- end row1-->


				<div class="row row2 clearfix" id="tab3_row2">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div>
				<!-- end row2-->
				
				
				<div class="row row1 clearfix" id="tab4_row1">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div><!-- end row1-->


				<div class="row row2 clearfix" id="tab4_row2">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div>
				<!-- end row2-->
				
				
				<div class="row row1 clearfix" id="tab5_row1">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div><!-- end row1-->


				<div class="row row2 clearfix" id="tab5_row2">
					<span class="videoBoxTitle">Demo Playlist</span> <a class="prev"></a><a class="next"></a>
					<div class="scrollable">
						You can add more content here
					</div>
					<!-- end scrollable-->
				</div>
				<!-- end row2-->
				
			</div><!-- end mediaContent-->
		</div><!-- end #videoboxContent-->
	</div><!-- end #kaltura-->
</body>
</html>
