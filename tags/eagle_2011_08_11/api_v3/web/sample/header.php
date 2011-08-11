<div id="header">
	<h1 class="logo">Your Site Logo Here</h1>
	<h4>These screens provide samples of ways you can incorporate Kaltura's platform on your site: Media galleries, UGC uploads & UGC mixing.</h4>
	<ul id="menu">
		<?php 
			$currentPage = pathinfo($_SERVER["PHP_SELF"], PATHINFO_BASENAME);
		?>
		<li><a href="media_gallery.php" <?php echo ($currentPage == "media_gallery.php") || (@$_GET["from"] == "media") ? "class=\"active\"" : "" ?>>Gallery (Media)</a></li>
		<li><a href="mix_gallery.php" <?php echo ($currentPage == "mix_gallery.php") || (@$_GET["from"] == "mix") ? "class=\"active\"" : "" ?>>Gallery (Mixes)</a></li>
		<li><a href="add_media.php" <?php echo ($currentPage == "add_media.php") ? "class=\"active\"" : "" ?>>Add Media</a></li>
		<li><a href="add_mix.php" <?php echo ($currentPage == "add_mix.php") ? "class=\"active\"" : "" ?>>Add Mix</a></li>
	</ul>
</div><!-- end header-->