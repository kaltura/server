<?php
ini_set('memory_limit','1024M');

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Restore Short Links ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/short_links_file ] [realrun / dryrun]' . PHP_EOL;
	echo ' File format: One short link URL per line, e.g.:' . PHP_EOL;
	echo '   http://www.kaltura.com/tiny/0003v' . PHP_EOL;
	echo '   https://www.kaltura.com/tiny/abc123' . PHP_EOL;
	die(' Error: missing short links file' . PHP_EOL . PHP_EOL);
}

if (!is_file($argv[1]))
{
	die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}

$shortLinks = file($argv[1]) or die (' Error: cannot read file at: "' . $argv[1] .'"' . PHP_EOL);

require_once (dirname(__FILE__) . '/../bootstrap.php');

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$count = 0;
$totalShortLinks = count($shortLinks);
$successCount = 0;
$errorCount = 0;
$skippedCount = 0;

foreach ($shortLinks as $shortLinkUrl)
{
	$shortLinkUrl = trim($shortLinkUrl);

	if (empty($shortLinkUrl) || $shortLinkUrl[0] == '#')
	{
		// Skip empty lines or comments
		continue;
	}

	// Extract short link ID from URL
	// Expected formats:
	// http://www.kaltura.com/tiny/0003v
	// https://www.kaltura.com/tiny/abc123
	// Or just the ID itself: 0003v
	$shortLinkId = null;

	if (preg_match('#/tiny/([a-z0-9]+)#i', $shortLinkUrl, $matches))
	{
		$shortLinkId = $matches[1];
	}
	elseif (preg_match('#^[a-z0-9]+$#i', $shortLinkUrl))
	{
		// Direct ID without URL
		$shortLinkId = $shortLinkUrl;
	}

	if (!$shortLinkId)
	{
		KalturaLog::err('ERROR: Could not extract short link ID from: [' . $shortLinkUrl . ']');
		$errorCount++;
		continue;
	}

	$count++;
	KalturaLog::debug('Processing short link [' . $count . '/' . $totalShortLinks . ']: ID [' . $shortLinkId . ']');

	// Retrieve short link without filters (to get even deleted/disabled ones)
	ShortLinkPeer::setUseCriteriaFilter(false);
	$shortLink = ShortLinkPeer::retrieveByPK($shortLinkId);
	ShortLinkPeer::setUseCriteriaFilter(true);

	if (!$shortLink)
	{
		KalturaLog::err('ERROR: Short link not found with ID [' . $shortLinkId . ']');
		$errorCount++;
		continue;
	}

	$currentStatus = $shortLink->getStatus();
	KalturaLog::debug('  Current status: ' . $currentStatus . ' (1=DISABLED, 2=ENABLED, 3=DELETED)');

	if ($currentStatus == ShortLinkStatus::ENABLED)
	{
		KalturaLog::debug('  Short link already ENABLED, skipping');
		$skippedCount++;
		continue;
	}

	// Set status to ENABLED
	$shortLink->setStatus(ShortLinkStatus::ENABLED);
	$shortLink->save();

	KalturaLog::info('  SUCCESS: Short link [' . $shortLinkId . '] set to ENABLED');
	$successCount++;

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();

	if ($count % 100 === 0)
	{
		KalturaLog::debug('Progress: ' . $count . ' out of: ' . $totalShortLinks);
		KalturaLog::debug('  Success: ' . $successCount . ', Skipped: ' . $skippedCount . ', Errors: ' . $errorCount);
		if ($count % 1000 === 0)
		{
			KalturaLog::debug('Sleeping for 10 seconds');
			sleep(10);
		}
	}
}

KalturaLog::info('Script Finished');
KalturaLog::info('Total processed: ' . $count);
KalturaLog::info('Successfully restored: ' . $successCount);
KalturaLog::info('Already enabled (skipped): ' . $skippedCount);
KalturaLog::info('Errors: ' . $errorCount);
