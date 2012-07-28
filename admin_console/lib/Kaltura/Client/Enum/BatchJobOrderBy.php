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

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Enum_BatchJobOrderBy
{
	const STATUS_ASC = "+status";
	const STATUS_DESC = "-status";
	const CHECK_AGAIN_TIMEOUT_ASC = "+checkAgainTimeout";
	const CHECK_AGAIN_TIMEOUT_DESC = "-checkAgainTimeout";
	const PROGRESS_ASC = "+progress";
	const PROGRESS_DESC = "-progress";
	const UPDATES_COUNT_ASC = "+updatesCount";
	const UPDATES_COUNT_DESC = "-updatesCount";
	const PRIORITY_ASC = "+priority";
	const PRIORITY_DESC = "-priority";
	const QUEUE_TIME_ASC = "+queueTime";
	const QUEUE_TIME_DESC = "-queueTime";
	const FINISH_TIME_ASC = "+finishTime";
	const FINISH_TIME_DESC = "-finishTime";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_DESC = "-fileSize";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const PROCESSOR_EXPIRATION_ASC = "+processorExpiration";
	const PROCESSOR_EXPIRATION_DESC = "-processorExpiration";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const LOCK_VERSION_ASC = "+lockVersion";
	const LOCK_VERSION_DESC = "-lockVersion";
}

