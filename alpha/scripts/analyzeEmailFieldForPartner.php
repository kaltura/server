<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if ($argc < 4) {
	echo "Example: php " . $argv[0] . " 12345 dryRun false false users.csv\n";
	die("Usage: php " . $argv[0] . " <partner id> <realRun | dryRun> <UpdateLoginEmail - true | false > <checkDuplications - if to check duplicate user Emails> <metadataProfileIds - optional comma separated list of metadata profile IDs>  <userList - optional csv file with puserIds column>\n");
}

$partnerId = $argv[1];
$dryRun = $argv[2] !== 'realRun';
$updateLoginEmail = filter_var($argv[3] ?? false, FILTER_VALIDATE_BOOLEAN);
$checkDuplications = filter_var($argv[4] ?? false, FILTER_VALIDATE_BOOLEAN);
$metadataProfileIds = [];
$userListCsv = '';

$metadataArg = $argv[5] ?? null;

if ($metadataArg !== null && is_readable($metadataArg) && !isset($argv[6])) {
	// No metadata IDs provided, only a CSV argument
	$userListCsv = $metadataArg;
} else {

	if ($metadataArg !== null) {
		$metadataProfileIds = $metadataArg !== '' ? explode(',', $metadataArg) : [];
	}

	$userListCsv = $argv[6] ?? '';
}



if (!$partner = PartnerPeer::retrieveByPK($partnerId)) {
	die("Please enter a valid partner Id!\n");
}

if (!empty($userListCsv) && !is_readable($userListCsv)) {
	die("User list file not found or not readable: $userListCsv\n");
}

KalturaLog::log('Starting to analyze users for partner [' . $partnerId . '] Run is a [' . $dryRun . '].' . (!empty($userListCsv) ? " Using user list from file [$userListCsv]." : ''));

$usersResult = getUsersByPartnerAndCsv($partnerId, $userListCsv);
$noEmailUsers = $usersResult['noEmailUsers'];
$withEmailUsers = $usersResult['withEmailUsers'];
$kuserEmails = $usersResult['kuserEmails'];

$totalUsers = count($noEmailUsers) + count($withEmailUsers);
$noUserPercentage = noEmailPercentage(count($noEmailUsers), $totalUsers);
KalturaLog::log("[$noUserPercentage%] of the users of partner [$partnerId] do not have an email address. exact numbers: [" . count($noEmailUsers) . " /$totalUsers]");
KalturaLog::log("[" . count($withEmailUsers) . "] users out of a total of [$totalUsers] users have email");
KalturaLog::log("[" . count($noEmailUsers) . "] users out of a total of [$totalUsers] users dont have email");

$report = updateUserForSharedUsers($withEmailUsers, $dryRun, $partner->getAdminSecret(), $updateLoginEmail);

KalturaLog::log('Users needing externalId update: ' . count($report['externalIdUpdates']));
KalturaLog::log('Users needing loginEmail update: ' . count($report['loginEmailUpdates']));

$duplicates = array();

if ($checkDuplications)
{
	KalturaLog::log('Checking for duplicated emails for partner [' . $partnerId . ']' . (!empty($userListCsv) ? ' By provided CSV List.' : '.'));
	$duplicates = countUsersWithDuplicatedEmail($partnerId, $kuserEmails ?? null, $metadataProfileIds);
}


$userUpdateReportFile = prepareAndWriteUserUpdateReport($withEmailUsers, $report, $duplicates, $partnerId, $metadataProfileIds, $checkDuplications);

KalturaLog::log("Done Running for partner [$partnerId]. Report file: $userUpdateReportFile");


function noEmailPercentage($noEmailUsersCount, $totalUsers): int {
    
	if ($totalUsers <= 0) {
        return 0;
    }

    return (int) floor(($noEmailUsersCount * 100) / $totalUsers);
}


function getUsers($partnerId, $hasEmail, $puserIds = null): array {
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::IS_ADMIN, 0, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::EMAIL, null, $hasEmail ? Criteria::ISNOTNULL : Criteria::ISNULL);

	if (!empty($puserIds)) {
		$emailCriteria->add(kuserPeer::PUSER_ID, $puserIds, Criteria::IN);
	}

	return kuserPeer::doSelect($emailCriteria);
}

function countUsersWithDuplicatedEmail($partnerId, $kuserEmails = null, $metadataProfileIds = []): array {

	KalturaLog::log('Counting user with email duplications .');

	$countField = 'COUNT(kuser.EMAIL)';
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::IS_ADMIN, array(0, 1), Criteria::IN);
	$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);

	if (!empty($kuserEmails)) {
		$emailCriteria->add(kuserPeer::EMAIL, $kuserEmails, Criteria::IN);
	}

	$emailCriteria->addGroupByColumn(kuserPeer::EMAIL);
	$emailCriteria->addSelectColumn($countField);
	$emailCriteria->addSelectColumn(kuserPeer::EMAIL);
	$emailCriteria->addHaving($emailCriteria->getNewCriterion(kuserPeer::EMAIL, $countField . '>' . 1, Criteria::CUSTOM));
	$stmt = kuserPeer::doSelectStmt($emailCriteria);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$duplicates = [];

	foreach($rows as $row) {
		KalturaLog::log("email [" . $row['EMAIL'] . "] is duplicated [" . $row['COUNT(kuser.EMAIL)'] . "] times");
		$duplicates[] = [
			$row['EMAIL'],
			$row['COUNT(kuser.EMAIL)']
		];

		$singleEmailCriteria = new Criteria();
		$singleEmailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
		$singleEmailCriteria->add(kuserPeer::IS_ADMIN, array(0,1), Criteria::IN);
		$singleEmailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		$singleEmailCriteria->add(kuserPeer::EMAIL, $row['EMAIL'] );

		$duplicateEmailUsers = kuserPeer::doSelect($singleEmailCriteria);
		kMemoryManager::clearMemory();

		foreach ($duplicateEmailUsers as $duplicateEmailUser)
		{
			$loginData = $duplicateEmailUser->getLoginData();
			$kuserId = $duplicateEmailUser->getId();

			$data = [
				'email' => $duplicateEmailUser->getEmail(),
				'puserId' => $duplicateEmailUser->getPuserId(),
				'kuserId' => $kuserId,
				'entryOwnedCount' => getOwnedEntryCount($kuserId, $partnerId),
				'loginEmail' => $loginData ? $loginData->getLoginEmail() : null,
				'firstName' => $duplicateEmailUser->getFirstName(),
				'lastName' => $duplicateEmailUser->getLastName(),
				'createdAt' => $duplicateEmailUser->getCreatedAt(),
				'externalId' => $duplicateEmailUser->getExternalId(),
				'isAdmin' => $duplicateEmailUser->getIsAdmin(),
			];

			$duplicates[] = $data;
		}
	}

	return $duplicates;
}

/**
 * Updates users for shared user scenarios by handling login emails and external IDs.
 * This function processes an array of users by comparing their login emails to an expected format
 * and updating them if necessary. It also sets the external ID to the user's email if it's not already defined.
 *
 * @param array $usersWithEmail An array of user objects that contain email information to process.
 * @param bool $dryRun If true, the function will simulate updates without performing actual changes.
 * @param string $adminSecret Secret key used for generating unique email addresses for shared users.
 * @param bool $updateLoginEmail Indicates whether login email updates should be performed.
 *
 * @return array An associative array containing two lists of updated users:
 *               'externalIdUpdates' for users whose external ID was updated,
 *               'loginEmailUpdates' for users whose login email was updated.
 * @throws PropelException
 */
function updateUserForSharedUsers(array $usersWithEmail, bool $dryRun, string $adminSecret, bool $updateLoginEmail): array {
	KalturaLog::log('Processing users with email for updates.');

	$report = ['externalIdUpdates' => [], 'loginEmailUpdates' => []];

	if (sizeof($usersWithEmail) > 0) {

		/* @var $user kuser */
		foreach($usersWithEmail as $user) {
			$KuserEmail = $user->getEmail();
			KalturaLog::log('Processing user [' . $KuserEmail . ']');
			// if need to update loginEmail. checks if it has loginData, then checks if the loginEmail is in the expected format for Shared Users, if not, updating it.

			if ($updateLoginEmail && ($loginData = $user->getLoginData())) {
				$loginEmail = $loginData->getLoginEmail();
				$uniqueEmail = uniqueLoginEmail($user->getPartnerId(), $adminSecret, $KuserEmail);

				if ($loginEmail === $uniqueEmail) {
					KalturaLog::log($loginEmail . " Match expected login Email for Shared Users - $uniqueEmail, no update needed." . PHP_EOL);
				} else {
					KalturaLog::log($loginEmail . " Does not match expected login Email for Shared Users - $uniqueEmail, updating the user loginData loginEmail" . PHP_EOL);
					$report['loginEmailUpdates'][] = $user->getId();

					if (!$dryRun) {
						$loginData->setLoginEmail($uniqueEmail);
						$loginData->save();
					}
				}
			}

			// If no external id, copy the email to external id, if no email, copy puserID if it's an email.
			if (!$user->getExternalId()) {

				$externalId = $user->getEmail();

				if (!$externalId) {
					KalturaLog::log('User [' . $user->getId() . '] has no email.');
					$isPuserEmail = filter_var($user->getPuserId(), FILTER_VALIDATE_EMAIL) !== false;

					if (!$isPuserEmail) {
						KalturaLog::log('User [' . $user->getId() . '] has no email and puserId is not an email either, skipping setting externalId.');
						continue;
					}

					$externalId = $user->getPuserId();
				}

				$report['externalIdUpdates'][] = $user->getId();

				if (!$dryRun) {
					KalturaLog::log('Copying email [' . $externalId . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
					$user->setExternalId($externalId);
					$user->save();
				} else {
					KalturaLog::log('Dry RUN - would copy email [' . $externalId . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
				}
			}

		}
	}
	kEventsManager::flushEvents();

	return $report;
}

/**
 * Generates a unique email login identifier matching with how KMS generate it for Shared Users Instances
 *
 * @param string $partnerId
 * @param string $adminSecret
 * @param string $email The email address whose domain is extracted and appended to the hash.
 * @return string A unique string combining the hash and the domain of the provided email.
 */
function uniqueLoginEmail(string $partnerId, string $adminSecret, string $email): string {
	$token = $partnerId . $adminSecret . $email;
	$constanthash = sha1($token);
	$domain = strstr($email, '@');

	return $constanthash . $domain;
}

function writeArrayToCsv($header, $rows): string {
	global $partnerId, $dryRun;

	$filename = ($dryRun ? 'DryRun' : 'RealRun') . "-$partnerId-external_user_update_report-" . date('Y-m-d_H-i-s') . '.csv';

	$fp = fopen($filename, 'a+');

	if ($fp === false) {
		throw new Exception("Cannot open file $filename for writing");
	}

	fputcsv($fp, $header);

	foreach($rows as $row) {
		fputcsv($fp, $row);
	}

	fclose($fp);
	KalturaLog::log("Report file saved to " . __DIR__ . "/" . $filename);

	return $filename;
}

/**
 * Builds the CSV headers for the user update report, including optional duplicate columns and metadata profile columns.
 *
 * @param bool $includeDuplicateColumns Whether to append duplicate count and entry ownership columns.
 * @param array $metadataProfileIds Metadata profile IDs that require role columns.
 * @return array
 */
function buildReportHeaders(bool $includeDuplicateColumns, array $metadataProfileIds): array
{
	$headers = ['kuserId', 'email', 'puserId', 'loginEmail', 'FirstName', 'LastName', 'CreatedAt', 'externalId', 'isAdmin', 'needsLoginEmailUpdate', 'needsExternalIdUpdate'];

	if ($includeDuplicateColumns) {
		$headers[] = 'duplicateEmailCount';
		$headers[] = 'entryOwnedCount';
	}

	foreach ($metadataProfileIds as $profileId) {
		$headers[] = 'metadataRole_' . $profileId;
	}

	return $headers;
}

/**
 * Normalizes duplicate data into lookup structures used for report enrichment.
 *
 * @param array $duplicates Raw duplicates information.
 * @param bool $includeDuplicateColumns Whether duplicate-specific columns will be emitted.
 * @return array{
 *     emailCounts: array<string,int>,
 *     byKuserId: array<int,array>,
 *     entryCounts: array<int,int>,
 *     userIds: array<int>
 * }
 */
function normalizeDuplicateData(array $duplicates, bool $includeDuplicateColumns): array
{
	$duplicateEmailCounts = [];
	$duplicatesByKuserId = [];
	$duplicateEntryCounts = [];

	foreach ($duplicates as $dup) {
		if (is_array($dup) && isset($dup[0], $dup[1]) && is_string($dup[0]) && is_numeric($dup[1])) {
			$duplicateEmailCounts[$dup[0]] = (int)$dup[1];
			continue;
		}

		if (!is_array($dup) || !isset($dup['kuserId'])) {
			continue;
		}

		$kuserId = $dup['kuserId'];
		$duplicatesByKuserId[$kuserId] = $dup;

		if ($includeDuplicateColumns) {
			$duplicateEntryCounts[$kuserId] = $dup['entryOwnedCount'] ?? 0;
		}
	}

	if (!$includeDuplicateColumns) {
		$duplicateEntryCounts = [];
	}

	return [
		'emailCounts' => $duplicateEmailCounts,
		'byKuserId' => $duplicatesByKuserId,
		'entryCounts' => $duplicateEntryCounts,
		'userIds' => array_keys($duplicatesByKuserId),
	];
}

/**
 * Resolves metadata roles for duplicated users and returns the final profile list.
 *
 * @param int $partnerId
 * @param array $duplicateUserIds
 * @param array $metadataProfileIds
 * @return array{roles: array<int,array>, profileIds: array<int>}
 */
function resolveMetadataRoles(int $partnerId, array $duplicateUserIds, array $metadataProfileIds): array
{
	$requestedProfileIds = !empty($metadataProfileIds) ? array_values(array_unique($metadataProfileIds)) : [];

	if (empty($duplicateUserIds)) {
		return [
			'roles' => [],
			'profileIds' => $requestedProfileIds,
		];
	}

	$metadataFetchResult = fetchMetadataRolesForUsers($partnerId, $duplicateUserIds, $requestedProfileIds);
	$metadataRolesByUser = $metadataFetchResult['roles'];
	$discoveredProfileIds = $metadataFetchResult['profileIds'];

	$finalProfileIds = !empty($requestedProfileIds) ? $requestedProfileIds : $discoveredProfileIds;
	$finalProfileIds = !empty($finalProfileIds) ? array_values(array_unique($finalProfileIds)) : [];

	return [
		'roles' => $metadataRolesByUser,
		'profileIds' => $finalProfileIds,
	];
}

/**
 * Builds report rows for users retrieved with email information.
 *
 * @param array $withEmailUsers
 * @param array $duplicateEmailCounts
 * @param array $duplicateEntryCounts
 * @param array $metadataProfileIds
 * @param array $metadataRolesByUser
 * @param array $loginEmailUpdateIds
 * @param array $externalIdUpdateIds
 * @param bool $includeDuplicateColumns
 * @param int $partnerId
 * @return array{rows: array<int,array>, processedUserIds: array<int,bool>}
 */
function buildRowsForUsersWithEmail(array $withEmailUsers, array $duplicateEmailCounts, array $duplicateEntryCounts, array $metadataProfileIds, array $metadataRolesByUser, array $loginEmailUpdateIds, array $externalIdUpdateIds, bool $includeDuplicateColumns, int $partnerId): array
{
	$rows = [];
	$processedUserIds = [];

	foreach ($withEmailUsers as $user) {
		$kuserId = $user->getId();
		$processedUserIds[$kuserId] = true;
		$puserId = $user->getPuserId();
		$loginEmail = $user->getLoginData() ? $user->getLoginData()->getLoginEmail() : '';
		$firstName = $user->getFirstName();
		$lastName = $user->getLastName();
		$createdAt = $user->getCreatedAt();
		$externalId = $user->getExternalId();
		$isAdmin = $user->getIsAdmin();
		$needsLoginEmailUpdate = isset($loginEmailUpdateIds[$kuserId]) ? 'yes' : 'no';
		$needsExternalIdUpdate = isset($externalIdUpdateIds[$kuserId]) ? 'yes' : 'no';
		$userEmail = $user->getEmail();
		$duplicateCount = $duplicateEmailCounts[$userEmail] ?? 0;
		$entryOwnedCount = $duplicateEntryCounts[$kuserId] ?? '';

		if ($includeDuplicateColumns && $entryOwnedCount === '' && $duplicateCount > 0) {
			$entryOwnedCount = getOwnedEntryCount($kuserId, $partnerId);
		}

		$row = [$kuserId, $userEmail, $puserId, $loginEmail, $firstName, $lastName, $createdAt, $externalId, $isAdmin, $needsLoginEmailUpdate, $needsExternalIdUpdate];

		if ($includeDuplicateColumns) {
			$row[] = $duplicateCount;
			$row[] = $entryOwnedCount;
		}

		if (!empty($metadataProfileIds)) {
			$hasDupMetadata = isset($metadataRolesByUser[$kuserId]);

			foreach ($metadataProfileIds as $profileId) {
				if ($hasDupMetadata) {
					$row[] = $metadataRolesByUser[$kuserId][$profileId] ?? 'not-found';
				} else {
					$row[] = 'not-checked';
				}
			}
		}

		$rows[] = $row;
	}

	return ['rows' => $rows, 'processedUserIds' => $processedUserIds];
}

/**
 * Builds report rows for duplicate users that were not part of the main email user list.
 *
 * @param array $duplicatesByKuserId
 * @param array $processedUserIds
 * @param array $duplicateEmailCounts
 * @param array $duplicateEntryCounts
 * @param array $metadataProfileIds
 * @param array $metadataRolesByUser
 * @param array $loginEmailUpdateIds
 * @param array $externalIdUpdateIds
 * @param bool $includeDuplicateColumns
 * @return array
 */
function buildRowsForDuplicateOnlyUsers(array $duplicatesByKuserId, array $processedUserIds, array $duplicateEmailCounts, array $duplicateEntryCounts, array $metadataProfileIds, array $metadataRolesByUser, array $loginEmailUpdateIds, array $externalIdUpdateIds, bool $includeDuplicateColumns): array
{
	$rows = [];

	foreach ($duplicatesByKuserId as $dupUserId => $dup) {
		if (isset($processedUserIds[$dupUserId])) {
			continue;
		}

		$email = $dup['email'] ?? '';
		$duplicateCount = $email !== '' ? ($duplicateEmailCounts[$email] ?? 0) : 0;

		$row = [
			$dupUserId,
			$email,
			$dup['puserId'] ?? '',
			$dup['loginEmail'] ?? '',
			$dup['firstName'] ?? '',
			$dup['lastName'] ?? '',
			$dup['createdAt'] ?? '',
			$dup['externalId'] ?? '',
			$dup['isAdmin'] ?? '',
			isset($loginEmailUpdateIds[$dupUserId]) ? 'yes' : 'no',
			isset($externalIdUpdateIds[$dupUserId]) ? 'yes' : 'no',
		];

		if ($includeDuplicateColumns) {
			$row[] = $duplicateCount;
			$row[] = $duplicateEntryCounts[$dupUserId] ?? ($dup['entryOwnedCount'] ?? 0);
		}

		if (!empty($metadataProfileIds)) {
			foreach ($metadataProfileIds as $profileId) {
				$row[] = $metadataRolesByUser[$dupUserId][$profileId] ?? 'not-found';
			}
		}

		$rows[] = $row;
	}

	return $rows;
}

/**
 * Prepares and writes a user update report to a CSV file. The report includes information
 * about user login emails, external IDs, whether updates are needed, and the count of duplicate emails.
 *
 * @param array $withEmailUsers An array of user objects with email information.
 * @param array $report An array containing 'loginEmailUpdates' and 'externalIdUpdates' mappings for users.
 * @param array $duplicates An array of duplicate records, where each record contains the email and its duplicate count.
 * @param int $partnerId Partner ID used when fetching metadata roles.
 * @param array $metadataProfileIds List of metadata profile IDs to include role columns for.
 * @return string The filename of the generated report.
 * @throws Exception
 */
function prepareAndWriteUserUpdateReport(array $withEmailUsers, array $report, array $duplicates, int $partnerId, array $metadataProfileIds = [], bool $includeDuplicateColumns = true): string
{
	$normalizedDuplicates = normalizeDuplicateData($duplicates, $includeDuplicateColumns);
	$duplicateEmailCounts = $normalizedDuplicates['emailCounts'];
	$duplicatesByKuserId = $normalizedDuplicates['byKuserId'];
	$duplicateEntryCounts = $normalizedDuplicates['entryCounts'];
	$duplicateUserIds = $normalizedDuplicates['userIds'];

	$metadataResolution = resolveMetadataRoles($partnerId, $duplicateUserIds, $metadataProfileIds);
	$metadataRolesByUser = $metadataResolution['roles'];
	$metadataProfileIds = $metadataResolution['profileIds'];
	$headers = buildReportHeaders($includeDuplicateColumns, $metadataProfileIds);

	$loginEmailUpdateIds = array_flip($report['loginEmailUpdates']);
	$externalIdUpdateIds = array_flip($report['externalIdUpdates']);
	$usersWithEmailRows = buildRowsForUsersWithEmail($withEmailUsers, $duplicateEmailCounts, $duplicateEntryCounts, $metadataProfileIds, $metadataRolesByUser, $loginEmailUpdateIds, $externalIdUpdateIds, $includeDuplicateColumns, $partnerId);
	$reportRows = $usersWithEmailRows['rows'];
	$processedUserIds = $usersWithEmailRows['processedUserIds'];

	$duplicateOnlyRows = buildRowsForDuplicateOnlyUsers($duplicatesByKuserId, $processedUserIds, $duplicateEmailCounts, $duplicateEntryCounts, $metadataProfileIds, $metadataRolesByUser, $loginEmailUpdateIds, $externalIdUpdateIds, $includeDuplicateColumns);
	array_push($reportRows, ...$duplicateOnlyRows);

	return writeArrayToCsv($headers, $reportRows);
}

/**
 * Retrieves users based on partner ID or an optional CSV file containing user IDs.
 *
 * If the CSV file is not provided, retrieves all users with and without email for the specified partner ID.
 * If the CSV file is provided, processes user IDs in chunks from the file and retrieves users accordingly.
 *
 * @param int $partnerId
 * @param string|null $userListCsv Optional. Path to the CSV file containing a list of user IDs. If null, all users for the partner are retrieved.
 * @return array Associative array containing:
 *               - 'noEmailUsers': Array of users without associated email addresses.
 *               - 'withEmailUsers': Array of users with associated email addresses.
 *               - 'kuserEmails': Array of email addresses extracted from 'withEmailUsers', or null if no CSV file is provided.
 */
function getUsersByPartnerAndCsv(int $partnerId, string $userListCsv = null): array {

	if (empty($userListCsv)) {
		$noEmailUsers = getUsers($partnerId, false);
		$withEmailUsers = getUsers($partnerId, true);
	} else {
		$userList = file($userListCsv, FILE_IGNORE_NEW_LINES);
		$userListChunk = array_chunk($userList, 100);
		$noEmailUsers = [];
		$withEmailUsers = [];

		foreach($userListChunk as $puserIds) {
			KalturaLog::log('Processing user IDs in chunk: [' . implode(',', $puserIds) . ']');
			$chunkNoEmailUsers = getUsers($partnerId, false, $puserIds);
			$chunkWithEmailUsers = getUsers($partnerId, true, $puserIds);

			if (!empty($chunkNoEmailUsers)) {
				array_push($noEmailUsers, ...$chunkNoEmailUsers);
			}

			if (!empty($chunkWithEmailUsers)) {
				array_push($withEmailUsers, ...$chunkWithEmailUsers);
			}
		}

		$kuserEmails = array_map(function ($user) {
			return $user->getEmail();
		}, $withEmailUsers);
	}

	return ['noEmailUsers' => $noEmailUsers, 'withEmailUsers' => $withEmailUsers, 'kuserEmails' => $kuserEmails ?? null];
}

function fetchMetadataRolesForUsers(int $partnerId, array $kuserIds, array $metadataProfileIds = [], int $chunkSize = 100): array {
	$rolesByUser = [];
	$profileIdsFound = [];

	if (empty($kuserIds)) {
		$initialProfiles = !empty($metadataProfileIds) ? array_values(array_unique($metadataProfileIds)) : [];
		return [
			'roles' => [],
			'profileIds' => $initialProfiles
		];
	}

	$uniqueUserIds = array_values(array_unique(array_filter($kuserIds)));
	$requestedProfileIds = !empty($metadataProfileIds) ? array_values(array_unique($metadataProfileIds)) : [];

	foreach (array_chunk($uniqueUserIds, $chunkSize) as $userIdsChunk) {
		$chunkRoles = [];

		foreach ($userIdsChunk as $id) {
			$chunkRoles[$id] = [];
		}

		$metadataCriteria = new Criteria();
		$metadataCriteria->add(MetadataPeer::PARTNER_ID, $partnerId);
		$metadataCriteria->add(MetadataPeer::OBJECT_ID, $userIdsChunk, Criteria::IN);
		$metadataCriteria->add(MetadataPeer::OBJECT_TYPE, MetadataObjectType::USER);
		$metadataCriteria->add(MetadataPeer::STATUS, Metadata::STATUS_VALID);

		if (!empty($requestedProfileIds)) {
			$metadataCriteria->add(MetadataPeer::METADATA_PROFILE_ID, $requestedProfileIds, Criteria::IN);
		}

		$metadataObjects = MetadataPeer::doSelect($metadataCriteria);

		foreach ($metadataObjects as $metadataObject) {
			/* @var $metadataObject Metadata */

			$key = $metadataObject->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			$xml = kFileSyncUtils::file_get_contents($key, true, false);

			$simpleXml = new SimpleXMLElement($xml);
			$roleNodes = $simpleXml->xpath('.//role');
			$roleValue = isset($roleNodes[0]) ? strval($roleNodes[0]) : '';
			$objectId = (int)$metadataObject->getObjectId();
			$profileId = (int)$metadataObject->getMetadataProfileId();

			if (!isset($chunkRoles[$objectId])) {
				$chunkRoles[$objectId] = [];
			}

			$chunkRoles[$objectId][$profileId] = $roleValue;
			$profileIdsFound[$profileId] = true;
		}

		foreach ($chunkRoles as $userId => $roles) {
			if (!isset($rolesByUser[$userId])) {
				$rolesByUser[$userId] = $roles;
			} else {
				$rolesByUser[$userId] = array_replace($rolesByUser[$userId], $roles);
			}
		}

		kMemoryManager::clearMemory();
	}

	if (!empty($requestedProfileIds)) {
		$profileIds = $requestedProfileIds;
	} else {
		$profileIds = array_values(array_unique(array_keys($profileIdsFound)));
	}

	return [
		'roles' => $rolesByUser,
		'profileIds' => $profileIds
	];
}

function getOwnedEntryCount($kuserId, $partnerId) {
	// check for owned entries per user id and count them
	/* @var $kuserId kuser */
	$countField = 'COUNT(entry.ID)';
	$ownedEntryCriteria = new Criteria();
	$ownedEntryCriteria->add(entryPeer::PARTNER_ID, $partnerId);
	$ownedEntryCriteria->add(entryPeer::KUSER_ID, $kuserId);
	$ownedEntryCriteria->add(entryPeer::STATUS, 2);
	$ownedEntryCriteria->addGroupByColumn(entryPeer::KUSER_ID);
	$ownedEntryCriteria->addSelectColumn($countField);
	$ownedEntryCriteria->addSelectColumn(entryPeer::ID);

	$stmt = entryPeer::doSelectStmt($ownedEntryCriteria);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $rows[0]['COUNT(entry.ID)'] ?? 0;
}
