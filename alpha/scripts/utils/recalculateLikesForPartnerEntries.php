<?php
require_once(__DIR__ . '/../bootstrap.php');

if ($argc !== 2)
{
	echo 'usage: php ' . $_SERVER['SCRIPT_NAME'] . " [partner_id]" . PHP_EOL;
	die;
}
$partnerId = $argv[1];
$class = new LikesReCalculator($partnerId);
$class->run();

class LikesReCalculator
{
	/**
	 * @var Partner
	 */
	protected $_partner;

	public function __construct($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			throw new Exception("Partner [$partnerId] not found");

		$this->_partner = $partner;
	}

	public function run()
	{
		$entryCriteria = new Criteria();
		$entryCriteria->add(entryPeer::PARTNER_ID, $this->_partner->getId());

		$entriesCount = entryPeer::doCount($entryCriteria);
		$this->writeLine('--------------------------------');
		$this->writeLine('Found '.$entriesCount.' entries');
		$this->writeLine('*** WARNING ***');
		$this->writeLine('Please make sure to back up the output of the following SQL query before running the script');
		$this->writeLine('SELECT id,votes,total_rank,rank FROM entry WHERE partner_id = '.$this->_partner->getId());
		$this->writeLine('--------------------------------');
		$continue = $this->promptToContinue();
		if (!$continue)
			return;

		$entryCriteria->setLimit(1000);

		$entries = entryPeer::doSelect($entryCriteria);
		while(count($entries))
		{
			foreach($entries as $entry)
			{
				$this->calculateLikesForEntry($entry);
			}

			$entryCriteria->setOffset($entryCriteria->getOffset() + count($entries));
			kMemoryManager::clearMemory();
			$entries = entryPeer::doSelect($entryCriteria);
		}

		$this->writeLine('Done');
	}

	protected function calculateLikesForEntry(entry $entry)
	{
		$currentData = array($entry->getVotes(), $entry->getTotalRank(), $entry->getRank());
		$this->writeLine('Calculating likes for entry '.$entry->getId().' ('.implode('|', $currentData).')');

		$c = new Criteria();
		$c->add(kvotePeer::PARTNER_ID, $this->_partner->getId());
		$c->add(kvotePeer::ENTRY_ID, $entry->getId());
		$c->add(kvotePeer::KVOTE_TYPE, KVoteType::LIKE);
		$c->add(kvotePeer::STATUS, KVoteStatus::VOTED);
		$numOfLikes = kvotePeer::doCount($c);

		$entry->setVotes($numOfLikes);
		$entry->setTotalRank($numOfLikes);
		$entry->setRank($numOfLikes ? 1 : 0);
		$entry->save();
	}

	protected function promptToContinue()
	{
		$this->writeLine('Continue? (y/n)');
		$input = trim(fgets(STDIN));
		if (strtolower($input) === 'y')
			return true;
		else
			return false;
	}

	protected function writeLine($msg)
	{
		echo $msg.PHP_EOL;
	}
}
