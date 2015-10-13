<?php
	const LIMIT = 1;
	const INITIAL_CREATED_AT_VALUE = '2000-01-01 00:00:00';

	require_once(__dir__ . "/../../../alpha/scripts/bootstrap.php");
	$c = new Criteria();
	$c->addAscendingOrderByColumn(kvotePeer::CREATED_AT);
	$c->setLimit(LIMIT);
	
	$createdAtValue = INITIAL_CREATED_AT_VALUE;
	$kVotes = array(1);
	while(!empty($kVotes))
	{
		$c->add(kvotePeer::CREATED_AT, $createdAtValue, Criteria::GREATER_THAN);
		kvotePeer::setUseCriteriaFilter(false);
		$kVotes = kvotePeer::doSelect($c);
		kvotePeer::setUseCriteriaFilter(true);

		foreach($kVotes as $kVote)
		{
			KalturaLog::debug("XXXXXXXXX - 1 - kvote - " . print_r($kVote, true));
			$kuserId = $kVote->getKuserId();
			kuserPeer::setUseCriteriaFilter(false);
			$kuser = kuserPeer::retrieveByPKNoFilter($kuserId);
			kuserPeer::setUseCriteriaFilter(true);

			KalturaLog::debug("XXXXXXXXX - 1 - kuser - " . print_r($kuser, true));

			if(!$kuser)
			{
				KalturaLog::err("no user found with id $kuserId");
				continue;
			}
			$puserId = $kuser->getPuserId();
			$kVote->setPuserId($puserId);
			$kVote->save();
		}
		KalturaLog::debug("created is - " . $kVote->getCreatedAt());
		$createdAtValue = $kVote->getCreatedAt();
	}
