<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

abstract class Kaltura_Client_Type_BatchJobBaseFilter extends Kaltura_Client_Type_Filter
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchJobBaseFilter';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->idEqual))
			$this->idEqual = (int)$xml->idEqual;
		if(count($xml->idGreaterThanOrEqual))
			$this->idGreaterThanOrEqual = (int)$xml->idGreaterThanOrEqual;
		if(count($xml->partnerIdEqual))
			$this->partnerIdEqual = (int)$xml->partnerIdEqual;
		$this->partnerIdIn = (string)$xml->partnerIdIn;
		$this->partnerIdNotIn = (string)$xml->partnerIdNotIn;
		if(count($xml->createdAtGreaterThanOrEqual))
			$this->createdAtGreaterThanOrEqual = (int)$xml->createdAtGreaterThanOrEqual;
		if(count($xml->createdAtLessThanOrEqual))
			$this->createdAtLessThanOrEqual = (int)$xml->createdAtLessThanOrEqual;
		if(count($xml->updatedAtGreaterThanOrEqual))
			$this->updatedAtGreaterThanOrEqual = (int)$xml->updatedAtGreaterThanOrEqual;
		if(count($xml->updatedAtLessThanOrEqual))
			$this->updatedAtLessThanOrEqual = (int)$xml->updatedAtLessThanOrEqual;
		if(count($xml->lockExpirationGreaterThanOrEqual))
			$this->lockExpirationGreaterThanOrEqual = (int)$xml->lockExpirationGreaterThanOrEqual;
		if(count($xml->lockExpirationLessThanOrEqual))
			$this->lockExpirationLessThanOrEqual = (int)$xml->lockExpirationLessThanOrEqual;
		if(count($xml->executionAttemptsGreaterThanOrEqual))
			$this->executionAttemptsGreaterThanOrEqual = (int)$xml->executionAttemptsGreaterThanOrEqual;
		if(count($xml->executionAttemptsLessThanOrEqual))
			$this->executionAttemptsLessThanOrEqual = (int)$xml->executionAttemptsLessThanOrEqual;
		if(count($xml->lockVersionGreaterThanOrEqual))
			$this->lockVersionGreaterThanOrEqual = (int)$xml->lockVersionGreaterThanOrEqual;
		if(count($xml->lockVersionLessThanOrEqual))
			$this->lockVersionLessThanOrEqual = (int)$xml->lockVersionLessThanOrEqual;
		$this->entryIdEqual = (string)$xml->entryIdEqual;
		$this->jobTypeEqual = (string)$xml->jobTypeEqual;
		$this->jobTypeIn = (string)$xml->jobTypeIn;
		$this->jobTypeNotIn = (string)$xml->jobTypeNotIn;
		if(count($xml->jobSubTypeEqual))
			$this->jobSubTypeEqual = (int)$xml->jobSubTypeEqual;
		$this->jobSubTypeIn = (string)$xml->jobSubTypeIn;
		$this->jobSubTypeNotIn = (string)$xml->jobSubTypeNotIn;
		if(count($xml->statusEqual))
			$this->statusEqual = (int)$xml->statusEqual;
		$this->statusIn = (string)$xml->statusIn;
		$this->statusNotIn = (string)$xml->statusNotIn;
		if(count($xml->abortEqual))
			$this->abortEqual = (int)$xml->abortEqual;
		if(count($xml->checkAgainTimeoutGreaterThanOrEqual))
			$this->checkAgainTimeoutGreaterThanOrEqual = (int)$xml->checkAgainTimeoutGreaterThanOrEqual;
		if(count($xml->checkAgainTimeoutLessThanOrEqual))
			$this->checkAgainTimeoutLessThanOrEqual = (int)$xml->checkAgainTimeoutLessThanOrEqual;
		if(count($xml->priorityGreaterThanOrEqual))
			$this->priorityGreaterThanOrEqual = (int)$xml->priorityGreaterThanOrEqual;
		if(count($xml->priorityLessThanOrEqual))
			$this->priorityLessThanOrEqual = (int)$xml->priorityLessThanOrEqual;
		if(count($xml->priorityEqual))
			$this->priorityEqual = (int)$xml->priorityEqual;
		$this->priorityIn = (string)$xml->priorityIn;
		$this->priorityNotIn = (string)$xml->priorityNotIn;
		if(count($xml->bulkJobIdEqual))
			$this->bulkJobIdEqual = (int)$xml->bulkJobIdEqual;
		$this->bulkJobIdIn = (string)$xml->bulkJobIdIn;
		$this->bulkJobIdNotIn = (string)$xml->bulkJobIdNotIn;
		if(count($xml->parentJobIdEqual))
			$this->parentJobIdEqual = (int)$xml->parentJobIdEqual;
		$this->parentJobIdIn = (string)$xml->parentJobIdIn;
		$this->parentJobIdNotIn = (string)$xml->parentJobIdNotIn;
		if(count($xml->rootJobIdEqual))
			$this->rootJobIdEqual = (int)$xml->rootJobIdEqual;
		$this->rootJobIdIn = (string)$xml->rootJobIdIn;
		$this->rootJobIdNotIn = (string)$xml->rootJobIdNotIn;
		if(count($xml->queueTimeGreaterThanOrEqual))
			$this->queueTimeGreaterThanOrEqual = (int)$xml->queueTimeGreaterThanOrEqual;
		if(count($xml->queueTimeLessThanOrEqual))
			$this->queueTimeLessThanOrEqual = (int)$xml->queueTimeLessThanOrEqual;
		if(count($xml->finishTimeGreaterThanOrEqual))
			$this->finishTimeGreaterThanOrEqual = (int)$xml->finishTimeGreaterThanOrEqual;
		if(count($xml->finishTimeLessThanOrEqual))
			$this->finishTimeLessThanOrEqual = (int)$xml->finishTimeLessThanOrEqual;
		if(count($xml->errTypeEqual))
			$this->errTypeEqual = (int)$xml->errTypeEqual;
		$this->errTypeIn = (string)$xml->errTypeIn;
		$this->errTypeNotIn = (string)$xml->errTypeNotIn;
		if(count($xml->errNumberEqual))
			$this->errNumberEqual = (int)$xml->errNumberEqual;
		$this->errNumberIn = (string)$xml->errNumberIn;
		$this->errNumberNotIn = (string)$xml->errNumberNotIn;
		if(count($xml->estimatedEffortLessThan))
			$this->estimatedEffortLessThan = (int)$xml->estimatedEffortLessThan;
		if(count($xml->estimatedEffortGreaterThan))
			$this->estimatedEffortGreaterThan = (int)$xml->estimatedEffortGreaterThan;
		if(count($xml->schedulerIdEqual))
			$this->schedulerIdEqual = (int)$xml->schedulerIdEqual;
		$this->schedulerIdIn = (string)$xml->schedulerIdIn;
		$this->schedulerIdNotIn = (string)$xml->schedulerIdNotIn;
		if(count($xml->workerIdEqual))
			$this->workerIdEqual = (int)$xml->workerIdEqual;
		$this->workerIdIn = (string)$xml->workerIdIn;
		$this->workerIdNotIn = (string)$xml->workerIdNotIn;
		if(count($xml->batchIndexEqual))
			$this->batchIndexEqual = (int)$xml->batchIndexEqual;
		$this->batchIndexIn = (string)$xml->batchIndexIn;
		$this->batchIndexNotIn = (string)$xml->batchIndexNotIn;
		if(count($xml->lastSchedulerIdEqual))
			$this->lastSchedulerIdEqual = (int)$xml->lastSchedulerIdEqual;
		$this->lastSchedulerIdIn = (string)$xml->lastSchedulerIdIn;
		$this->lastSchedulerIdNotIn = (string)$xml->lastSchedulerIdNotIn;
		if(count($xml->lastWorkerIdEqual))
			$this->lastWorkerIdEqual = (int)$xml->lastWorkerIdEqual;
		$this->lastWorkerIdIn = (string)$xml->lastWorkerIdIn;
		$this->lastWorkerIdNotIn = (string)$xml->lastWorkerIdNotIn;
		if(count($xml->dcEqual))
			$this->dcEqual = (int)$xml->dcEqual;
		$this->dcIn = (string)$xml->dcIn;
		$this->dcNotIn = (string)$xml->dcNotIn;
	}
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $idGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockExpirationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockExpirationLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $jobTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobSubTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeNotIn = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $abortEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeoutGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeoutLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $bulkJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rootJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobErrorTypes
	 */
	public $errTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumberEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $estimatedEffortLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $estimatedEffortGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $schedulerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $schedulerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $workerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $workerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndexEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $batchIndexIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $batchIndexNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastSchedulerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastSchedulerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastSchedulerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastWorkerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastWorkerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastWorkerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcNotIn = null;


}

