<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');
// revert kuser_id 'isAdmin' and 'loginEnabled' = 'login_data_id' to false

if ($argc < 2)
{
	echo "\n ========= Revert KMC admin to 'regular' user (not admin) ========= \n";
	die (" Missing required parameters:\n php " . $argv[0] . " {kuser_id || kuser,id,csv || path/to/kuser_id_list.txt} [realrun / dryrun]\n\n");
}

const UNIX_LINE_END = "\n";

if(is_file($argv[1]))
{
	$kuserIds = file($argv[1]) or die ('Could not read file from path: ' . $argv[1] . UNIX_LINE_END);
}
elseif (strpos($argv[1], ','))
{
	$kuserIds = explode(',', $argv[1]);
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