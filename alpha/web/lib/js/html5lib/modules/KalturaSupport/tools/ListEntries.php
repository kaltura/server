<?php 
 
// Load Kaltura API v3
include_once(  dirname( __FILE__ ) . '/../kaltura_client_v3/KalturaClient.php' );

$Entries = new EntriesList;

if( (isset($_POST['partnerId']) && isset($_POST['adminSecret'])) ||
 	(isset($_POST['email']) && isset($_POST['password'])) ) {
 	// Do login
 	$Entries->doLogin();
}

// If we have KS show the entries list
if( isset($_GET['ks']) ) {
	$results = $Entries->getEntriesByKs();
} else {
	$results = array();
}

// Output the page
$Entries->outputPage($results);

class EntriesList {
	
	var $page = 1;
	var $perPage = 25;
	var $maxPages = 1;
	var $totalEntries = 0;
	var $partnerId = 0;
	var $ks = false;
	var $error = false;
	
	function setPage( $p ) {
		$this->page = $p;
		$this->page = ( $this->page < 2 ) ? 1 : $this->page;
	}
	
	function doLogin() {
 			
 		// Display error message if some values are missing
 		if( ( empty($_POST['partnerId']) || empty($_POST['adminSecret']) ) && ( 
 				empty($_POST['email']) && empty($_POST['password']) ) ) {
 			$this->error = 'Partner ID / Admin Secret is missing.'; 
 			return false;
 		}
		if( ( empty($_POST['email']) || empty($_POST['password']) ) && (
				empty($_POST['partnerId']) && empty($_POST['adminSecret']) ) ) {
 			$this->error = 'Email / Password is missing.'; 
			return false;
 		}		
 			
		// Create KS
		$this->partnerId = intval($_POST['partnerId']);
		
		$ks = $this->createKs( $this->partnerId, htmlspecialchars($_POST['adminSecret']), htmlspecialchars($_POST['email']), htmlspecialchars($_POST['password']) );
		
		// Create URL
		$url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['SCRIPT_NAME'] . '?ks=' . $ks;
		header("location: ". $url);
		exit();

	}
	
	function hasKs() {
		return $this->ks;
	}
	
	// Create a new KS
	// Required parameters are: $partnerId & $secret -OR- $email & $password
	function createKs( $partnerId, $secret, $email, $password ) {
		
		// Some default vars for session start
		$userId = 0;
		$type = 2;
		$expiry = 43200;
		$privileges = null;
		
		// Start client
		$conf = new KalturaConfiguration( $partnerId );
		$client = new KalturaClient( $conf );
		
		try {
			if( $partnerId && $secret ) {
				// Get KS using client & secret
				$session = $client->session->start($secret, $userId, $type, $partnerId, $expiry, $privileges);
			} elseif( !( empty($email) && empty($password) ) ) {
				// Get KS using email & password
				$session = $client->adminUser->login($email, $password);
			} else {
				die('Some fields are missing');
			}
			
		} catch( Exception $e ){
			$this->error = 'Problem with creating a KS. <a href="'.$_SERVER['SCRIPT_NAME'].'">Try again</a>';
			die($this->error);
			return false;
		}
		
		return $session;		
	}
	
	// Return a Client with KS
	function getClient( ) {
		try {		
			$conf = new KalturaConfiguration( $this->partnerId );
			$client = new KalturaClient( $conf );
			$client->setKS( $this->ks );
			
		} catch( Exception $e ){
			
			$this->error = 'Error setting KS. <a href="'.$_SERVER['SCRIPT_NAME'].'">Try again</a>';
			die($this->error);
			return false;
		}
		return $client;		
	}
	
	function getEntriesByKs( ) {
		
		// Set Ks
		$this->ks = htmlspecialchars($_GET['ks']);
		
		// Set Partner Id from Ks
		$splitKs = explode(";", base64_decode($this->ks));
		$this->partnerId = $splitKs[1];
		
		// Set the page
		$this->setPage( (isset($_GET['page'])) ? intval($_GET['page']) : 1 );		
		
		try {
			// Get Client
			$client = $this->getClient();
			
			// Set Filter		
			$filter = new KalturaMediaEntryFilter();
			$filter->mediaTypeEqual = '1';
			
			// Set Pager
			$pager = new KalturaFilterPager();
			$pager->pageSize = $this->perPage;
			$pager->pageIndex = $this->page;
		
			// Get Results	
			$results = $client->baseEntry->listAction($filter, $pager);	
			$this->totalEntries = $results->totalCount;		
			
			// Set Max Pages
			$this->maxPages = ceil($this->totalEntries / $this->perPage);		
			
			return $results->objects;
			
		} catch( Exception $e ){
			$this->error = 'Invalid KS. Please <a href="'.$_SERVER['SCRIPT_NAME'].'">login</a> again.';
			die($this->error);
			return array();
		}		
	}

	function outputPage($results) {
		
		$url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['SCRIPT_NAME'] . '?ks=' . $this->ks;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Entries List</title>    
	<script type="text/javascript" src="../../../mwEmbedLoader.js"></script> 
	<script type="text/javascript">
	var partnerId = <?php echo $this->partnerId; ?>;
	var page = <?php echo $this->page; ?>;
	var maxPages = <?php echo $this->maxPages; ?>;
	var prevUrl = '<?php echo $url . '&page=' . ($this->page - 1); ?>';
	var nextUrl = '<?php echo $url . '&page=' . ($this->page + 1); ?>';

		
	mw.ready( function(){

		mw.setConfig( 'Kaltura.IframeRewrite', true );
		mw.setConfig( 'EmbedPlayer.EnableIpadHTMLControls', true);
		mw.setConfig( 'EmbedPlayer.OverlayControls', true );		
		
		$j('.entry').click( function(){
			$j(window).scrollTop(0);
			var entryId = this.hash.slice(1);
			var url = '../../../mwEmbedFrame.php/entry_id/' + entryId + '/wid/_' + partnerId + '/autoplay/autoplay/' + mw.getKalturaIframeHash();

			$j( '#videoContainer iframe' ).css('display', 'block');
			$j( '#videoContainer iframe' ).attr('src', url);

		});

		if( page <= 1) {
			$j( '.navigation a[hash=#prev]' ).hide();
		} else {
			$j( '.navigation a[hash=#prev]' ).click( function() {
				window.location.href=prevUrl;
			} );
		}

		if( page >= maxPages) {
			$j( '.navigation a[hash=#next]' ).hide();
		} else {
			$j( '.navigation a[hash=#next]' ).click( function() {
				window.location.href=nextUrl;
			} );
		}
		
	} );
	</script>
	<style>
	.clear { clear: both; }
	.left { float: left; }
	.right { float: right; }
	#wrapper { width: 400px; }
	#videoContainer { width:400px; text-align: center; }
	#videoContainer iframe { display: none; }
	.navigation { clear: both; }
	ul { list-style-type: none; margin: 0; padding: 0; }
	li { clear: both; padding: 0 0 20px 0; }
	li img { border: 0; float: left; margin: 0 10px 4px 0; }
	li h4 { padding: 30px 0 0 0; }	
	</style>
</head>
<body>
<div id="wrapper">
	<h2> Kaltura HTML5 Player Demo </h2>
	<?php if( !($this->hasKs()) ) { ?>
		<?php if( $this->error ){ ?>
		<div><?php echo $this->error; ?></div>
		<?php } ?>
	<form method="post">
		<span style="width:300px;float:left">
			Partner Id: <input name="partnerId" size="15" /><br />
			Admin Secret: <input name="adminSecret" size="15" /><br />
			<strong>OR:</strong><br />
			Email: <input name="email" size="15" /><br />
			Password: <input name="password" type="password" size="15" /><br />			
			<input type="submit" value="Create URL" />
		</span>
	</form>
	<?php }  else {
	
		if( empty($results) ) { 
			echo 'No videos were found.';
		 } else { 
	?>
	<div id="videoContainer">
		<iframe width="400" height="299" frameborder="0" ></iframe>
	</div>
	<div class="clear"></div>
	<div class="navigation">
		<a class="left" href="#prev">Prev</a>
		<a class="right" href="#next">Next</a>
	</div>
	<ul>
	<?php 
			foreach($results as $res) {
				//print_r($res);
				$entry = '<li><a class="entry" href="#' . $res->id .'">';
				$entry .= '<img src="' . $res->thumbnailUrl . '" />';
				$entry .= '<h4>' . $res->name . '</h4></a></li>';
				echo $entry;
			}
	?>
	</ul>
	<div class="navigation">
		<a class="left" href="#prev">Prev</a>
		<a class="right" href="#next">Next</a>
	</div>
	<?php	
		 } 
	}
	?>
</div>
</body>
</html>
<?php
	}
}
?>
