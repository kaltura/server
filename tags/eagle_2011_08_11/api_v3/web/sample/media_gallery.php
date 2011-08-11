<?php
require_once("../../bootstrap.php");
require_once("config.php");
require_once("lib/KalturaClient.php");

$page = @$_GET["page"];
if (!$page) 
	$page = 1;
$pageSize = 12;

$config = new KalturaConfiguration(PARTNER_ID);
$config->serviceUrl = SERVER_URL;
$client = new KalturaClient($config);

$error = "";
try 
{
    $ks = $client->session->start(ADMIN_SECRET, "USERID", KalturaSessionType::ADMIN);
}
catch (Exception $ex)
{
    $error = $ex->getMessage();
}

if (!$error)
{
    $client->setKs($ks);
    
    $pager = new KalturaFilterPager();
    $pager->pageSize = $pageSize;
    $pager->pageIndex = $page;
    $filter = new KalturaMediaEntryFilter();
	$filter->orderBy = "-createdAt";
    try 
    {
        $response = $client->media->listAction($filter, $pager);
    }
    catch (Exception $ex)
    {
        $error = $ex->getMessage(); 
    }
    $count = $response->totalCount;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Kaltura Sample Kit - Gallery</title>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/swfobject.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
</head>
<body>
	<div id="wrap">
		<div id="contWrap" class="innerpage galleryPage">
			<div class="ipleftSide"></div>
			<div class="ipleftSide iprightSide"></div>
			<div id="row1" class="clearfix">
				<?php require("header.php"); ?>	
				<?php if ($error): ?>
					<p>
    					An error occured while trying to list media entries. <br />
    					Error message:
					</p> 
					<pre><?php echo $error; ?></pre>
				<?php else: ?>
    				<?php if (!count($response->objects)): ?>
    				<p>
    					No media entries found. <a href="add_media.php">Click here</a> to add your first media.
					</p>
    				<?php else: ?>
    				<ul id="entries" class="clearfix">
    				<?php foreach($response->objects as $mediaEntry): ?>
    					<li>
    						<div class="name">
    						<?php if ($mediaEntry->status === KalturaEntryStatus::IMPORT || $mediaEntry->status === KalturaEntryStatus::PRECONVERT): ?>
    							(Converting) <?php echo $mediaEntry->name; ?><br />
    						<?php elseif ($mediaEntry->status === KalturaEntryStatus::ERROR_CONVERTING): ?>
    							(Error Converting) <?php echo $mediaEntry->name; ?><br />
							<?php else: ?>
	   							<?php echo $mediaEntry->name; ?><br />
    						<?php endif; ?>
    						</div>
    						<div class="thumb">
    							<?php 
    								// this will make fixed size image filled with black color 
    								if ($mediaEntry->thumbnailUrl)
    									$thumbUrl = $mediaEntry->thumbnailUrl . "/width/120/height/90/type/0/bgcolor/000000";
    								else
    									$thumbUrl = "";
    							?>
    							<?php if ($mediaEntry->mediaType == KalturaMediaType::IMAGE && $mediaEntry->status == KalturaEntryStatus::READY): ?>
							<!-- entry ready and image, linking to image.php -->
    							<a href="image.php?entryId=<?php echo $mediaEntry->id; ?>&from=media"><img src="<?php echo $thumbUrl; ?>" /></a>
    							<?php elseif($mediaEntry->status == KalturaEntryStatus::READY): ?>
							<!-- entry ready and not image, linking to player -->
    							<a href="player.php?entryId=<?php echo $mediaEntry->id; ?>&from=media"><img src="<?php echo $thumbUrl; ?>" /></a>
    							<?php else: ?>
							<!-- entry not ready, no use of linking anywhere -->
    							<img src="<?php echo $thumbUrl; ?>" />
    							<?php endif; ?>
    						</div>
    					</li>
    				<?php endforeach; ?>
    				</ul>
    				
    				<div id="pager">
    				<?php
    					$total = ceil($count / $pageSize);
    					$current = $page;
    					$endSize = 1;
    					$midSize = 2;
    					$dots = false;
    
    					if ($total > 1):
    						for ($i = 1; $i <= $total; $i++ ):
    							if ($i == $current) :
    								echo "<span class='pages current'>$i</span>";
    								$dots = true;
    							else :
    								if ($i <= $endSize || ($current && $i >= $current - $midSize && $i <= $current + $midSize) || $i > $total - $endSize):
    									echo '<span class="pages"><a href="media_gallery.php?page='.$i.'">'.$i.'</a></span>';
    									$dots = true;
    								elseif ($dots) :
    									echo '<span class="pages dots">...</span>';
    									$dots = false;
    								endif;
    							endif;
    						endfor;
    					endif;
    				?>
    				</div>
    				<?php endif; ?>
				<?php endif; ?>
			</div><!-- end #row1 -->
		</div><!-- end #contWrap -->
		<div id="bottomCorners"><div></div></div>
		<?php include("footer_nav.php"); ?>
	</div>
</body>
</html>