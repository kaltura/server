<?php
if (!isset($activeTab))
	$activeTab = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_title(); include_metas(); ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="all" href="/css/corp_layout.css" />
	<link rel="stylesheet" type="text/css" media="all" href="/css/corp.css" />
	<link rel="stylesheet" type="text/css" media="print" href="/css/corp_layout_print.css" />
</head>
<body>
	<div id="wrap">
		<div id="header">
			<div class="leftSide"></div>
			<a href="<?php echo url_for('/corp'); ?>" class="logo" title="Kaltura &ndash; Creating Together"></a>
			<ul class="userMenu">	
				<li class="last"><a href='<?php echo url_for('/corp/contact'); ?>'>Contact</a></li>
				<?php
				/*
				 	$email = $sf_user->getAttribute('email');
					if($sf_user->isAuthenticated())
					{
						echo "<li><a href='".url_for('/login/signout')."'>Sign out</a></li>";
						echo "<li>Hello, <a href='".url_for('/corp/userzone_account')."'>". substr( $email, 0, 13 )."</a></li>";
					}
					else
				*/
					{
						echo "<li id='layout_signin'><a href='".url_for('/cms/login')."'>Partner Login</a></li>";
					}
				?>
			</ul>
<!-- Google CSE Search Box Begins  -->
<div style="display:none">
<form action="<?php echo url_for("/corp/search"); ?>" id="searchbox_014173296589949272966:uncsmpomy4e">
  <input type="hidden" name="cx" value="014173296589949272966:uncsmpomy4e" />
  <input type="hidden" name="cof" value="FORID:11" />
  <input id="googleQueryField" type="text" name="q" size="25" />
  <input type="submit" name="sa" value="Search" />
</form>
</div>
<script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=searchbox_014173296589949272966%3Auncsmpomy4e&lang=en"></script>
<!-- Google CSE Search Box Ends -->
			<div class="search"><input type="text" id="navBarSearchInput" value="Search Kaltura" /><div id="navBarSearcGo"></div></div>
			<ul class="navMenu <?php if ($activeTab == "home") echo "corpHome"; ?>">
				<?php
					$tabs = array("solutions_overview" => "Solutions",
						"technology" => "Technology",
						"partners" => "Partners &amp; Customers",
						"developers" => "Developers",
						"company" => "About Us",
						"userzone" => "User Zone" );
						
					if ($activeTab != "home")			
						$tabs ["download"] = "Download Center";

					if ($activeTab == "home")
					$tabs ["userzone"] = "<span>User Zone</span>";
					
					foreach($tabs as $key => $val){
						if($activeTab == $key)
							echo "<li class='active'>$val</li>";
						else
							echo "<li><b></b><a href='".url_for("/corp/$key")."'>$val</a></li>";
					}
				?>
			</ul>
			<?php if ($activeTab == "home") echo '<div id="bigButton"><a href="' . url_for('/corp/download') . '"></a><div></div></div>'; ?>
			<div class="leftSide rightSide"></div>
		</div><!-- end header-->
		<?php echo $sf_content ?>
	</div><!-- end wrap-->
	<div id="bottomCorners"><div></div></div>
	<div id="footer" class="clearfix">
		<div class="content">
			<p>
				Copyright © 2008 Kaltura Inc. 
				<br/>
				All Rights Reserved. Designated trademarks and brands are the property of their respective owners.
				<br/>
				Use of this web site constitutes acceptance of the <a href="/index.php/corp/tandc">Terms of Use</a> and <a href="/index.php/corp/privacy">Privacy Policy</a>.
				<br/><br />
				User submitted media on this site is licensed under:
				<br />
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-Share Alike 3.0 Unported License</a>.
				<br />
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="float:left; margin-top:20px;">
					<img alt="Creative Commons License" style="" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" />
				</a>
			</p>
		</div>
		<ul>
			<li><a href="<?php echo url_for('/corp/news'); ?>">News</a></li>
			<li><a href='http://kaltura.com/blog/'>Blog</a></li>
			<li><a href='http://www.kaltura.com/wiki/index.php/'>Developers Wiki</a></li>
			<li><a href="<?php echo url_for('/corp/contact'); ?>">Contact Us</a></li>	
		</ul>
	</div><!-- end footer-->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "UA-2078931-1";
urchinTracker();
</script>	

</body>
</html>