<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');

if ($argc < 3)
{
	die("Usage: php $argv[0] entryIdsFile referenceIdsTagFile.csv <realrun | dryrun> \n");
}

$entryIdsFile = $argv[1] ;
$referenceIdsTagFile = $argv[2];
$dryrun = true;
if($argc == 4 && $argv[3] == 'realrun')
	$dryrun = false;

$entryIds = file ($entryIdsFile) or die ('Could not read file'."\n");
$referenceIdsTagList = file ($referenceIdsTagFile) or die ('Could not read file'."\n");

foreach ($entryIds as $entryId)
{
	$entryId = trim($entryId);
	$entry = entryPeer::retrieveByPK($entryId);
	if ($entry)
	{
		$entryReferenceId = $entry->getReferenceID();
		foreach ($referenceIdsTagList as $referenceIdsTag)
		{
			$referenceIdsTag = trim($referenceIdsTag);
			list($referenceId, $tag) = explode(',', $referenceIdsTag);
			if ($referenceId && $tag && $entryReferenceId === $referenceId)
			{
				$pattern = array('/^/', '/[^[:alnum:]]/u');
				$replacement = array('__', '_');
				$tag = preg_replace($pattern, $replacement, $tag);
				if ($tag)
				{
					$entryTags = $entry->getTags();
					if ($entryTags)
					{
						$entryTags .= ',';
					}
					$entry->setTags($entryTags . $tag);
					$entry->save();
					print_r('Adding tag __' . $tag . ' to entryId: '. $entryId .
						' with reference id: ' . $entryReferenceId . "\n");
				}
			}
		}
	}
}
print_r("Done! \n");

