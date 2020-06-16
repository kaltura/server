<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	echo "\n This Script revert kuser_id 'isAdmin' and 'loginEnabled' = 'login_data_id' to false\n * Input: {kuser_id} = PK or path/to/list.ext\n\n";
	die (" Missing required parameters: php " . $argv[0] . " {kuser_id} [realrun / dryrun]\n\n");
}

const UNIX_LINE_END = "\n";

if(is_file($argv[1]))
{
	$kuserIds = file($argv[1]) or die ('Could not read file from path: ' . $argv[1] . UNIX_LINE_END);
}
else
{
	$kuserIds = array($argv[1]);
}

$dryRun = true;
if ($argc == 3 && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::debug('dryrun value: ['.$dryRun.']');

foreach ($kuserIds as $kuserId)
{
	$kuserId = trim($kuserId);
	KalturaLog::debug('Started handling kuser id: ' . $kuserId);
	$kuser = kuserPeer::retrieveByPK($kuserId);

	if ($kuser)
	{
		if (!$kuser->getIsAccountOwner())
		{
			try
			{
				$kuser->setRoleIds(null);
				$kuser->setIsAdmin(false);
				$kuser->disableLogin(); // $this->save() occurs inside this function

				if (!$dryRun)
				{
					kEventsManager::flushEvents();
				}
				KalturaLog::debug('Finished handling kuser id: ' . $kuserId);
			}
			catch (Exception $e)
			{
				KalturaLog::notice($e->getCode() . ' for kuser id: ' . $kuserId);
				KalturaLog::debug('Stopped handling kuser id: ' . $kuserId);
			}
		}
		else
		{
			KalturaLog::notice('Cannot modify KMC Account Owner kuser id: ' . $kuserId . ' please set new Account Owner then re-run script');
		}
	}
	else
	{
		KalturaLog::debug('Cannot find kuser id: ' . $kuserId);
	}
}