<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if ($argc < 4) {
	echo "Example: php " . $argv[0] . " 12345 dryRun false users.csv\n";
	die("Usage: php " . $argv[0] . " <partner id> <realRun | dryRun> <UpdateLoginEmail - true | false > <userList - optional csv file>\n");
}

$partnerId = $argv[1];
$dryRun = $argv[2] !== 'realRun';
$updateLoginEmail = filter_var($argv[3] ?? false, FILTER_VALIDATE_BOOLEAN);
$userListCsv = $argv[4];

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
KalturaLog::log('Checking for duplicated emails for partner [' . $partnerId . ']' . (!empty($userListCsv) ? ' By provided CSV List.' : '.'));
$duplicates = countUsersWithDuplicatedEmail($partnerId, $kuserEmails ?? null);

$userUpdateReportFile = prepareAndWriteUserUpdateReport($withEmailUsers, $report, $duplicates);

KalturaLog::log('Done.');


function noEmailPercentage($noEmailUsersCount, $totalUsers): int
{
	return (int)(($noEmailUsersCount * 100) / $totalUsers);
}

function getUsers($partnerId, $hasEmail, $puserIds = null): array
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::IS_ADMIN, 0, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::EMAIL, null, $hasEmail ? Criteria::ISNOTNULL : Criteria::ISNULL);

	if (!empty($puserIds)) {
		$emailCriteria->add(kuserPeer::PUSER_ID, $puserIds, Criteria::IN);
	}

	return kuserPeer::doSelect($emailCriteria);
}

function countUsersWithDuplicatedEmail($partnerId, $kuserEmails = null): array
{

	KalturaLog::log('Counting user with email duplications .');

	$countField = 'COUNT(kuser.EMAIL)';
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
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
	foreach ($rows as $row) {
		KalturaLog::log("email [" . $row['EMAIL'] . "] is duplicated [" . $row['COUNT(kuser.EMAIL)'] . "] times");
		$duplicates[] = [$row['EMAIL'], $row['COUNT(kuser.EMAIL)']];
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
function updateUserForSharedUsers(array $usersWithEmail, bool $dryRun, string $adminSecret, bool $updateLoginEmail): array
{
	KalturaLog::log('Processing users with email for updates.');

	$report = [
		'externalIdUpdates' => [],
		'loginEmailUpdates' => []
	];

	if (sizeof($usersWithEmail) > 0) {

		/* @var $user kuser */
		foreach ($usersWithEmail as $user) {
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

			// If no external id, copy the email to external id.
			if (!$user->getExternalId()) {
				KalturaLog::log('Copying email [' . $user->getEmail() . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
				$report['externalIdUpdates'][] = $user->getId();

				if (!$dryRun) {
					$user->setExternalId($user->getEmail());
					$user->save();
				}
			}
		}
	}
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
function uniqueLoginEmail(string $partnerId, string $adminSecret, string $email): string
{
	$token = $partnerId . $adminSecret . $email;
	$constanthash = sha1($token);
	$domain = strstr($email, '@');
	return $constanthash . $domain;
}

function writeArrayToCsv($filename, $header, $rows): string
{
	$timestamp = date('Ymd_His');
	$filenameWithTimestamp = preg_replace('/\.csv$/', "_{$timestamp}.csv", $filename);
	$fp = fopen($filenameWithTimestamp, 'w');

	if ($fp === false) {
		throw new Exception("Cannot open file $filenameWithTimestamp for writing");
	}

	fputcsv($fp, $header);

	foreach ($rows as $row) {
		fputcsv($fp, $row);
	}

	fclose($fp);
	KalturaLog::log("Report file saved to " . __DIR__ . "/" . $filenameWithTimestamp);
	return $filenameWithTimestamp;
}

/**
 * Prepares and writes a user update report to a CSV file. The report includes information
 * about user login emails, external IDs, whether updates are needed, and the count of duplicate emails.
 *
 * @param array $withEmailUsers An array of user objects with email information.
 * @param array $report An array containing 'loginEmailUpdates' and 'externalIdUpdates' mappings for users.
 * @param array $duplicates An array of duplicate records, where each record contains the email and its duplicate count.
 * @param string $baseFilename The name of the output CSV file (default: 'user_update_report.csv').
 * @return bool|string Returns true if the report was successfully written, otherwise false.
 * @throws Exception
 */
function prepareAndWriteUserUpdateReport(array $withEmailUsers, array $report, array $duplicates, string $baseFilename = 'user_update_report.csv'): bool|string
{
	// Build a map of email => duplicate count
	$duplicateEmailCounts = [];
	foreach ($duplicates as $dup) {
		$duplicateEmailCounts[$dup[0]] = $dup[1];
	}

	$loginEmailUpdateIds = array_flip($report['loginEmailUpdates']);
	$externalIdUpdateIds = array_flip($report['externalIdUpdates']);
	$reportRows = [];

	foreach ($withEmailUsers as $user) {
		$userId = $user->getId();
		$puserId = $user->getPuserId();
		$loginEmail = $user->getLoginData() ? $user->getLoginData()->getLoginEmail() : '';
		$externalId = $user->getExternalId();
		$needsLoginEmailUpdate = isset($loginEmailUpdateIds[$userId]) ? 'yes' : 'no';
		$needsExternalIdUpdate = isset($externalIdUpdateIds[$userId]) ? 'yes' : 'no';
		$userEmail = $user->getEmail();
		$duplicateCount = $duplicateEmailCounts[$userEmail] ?? 0;
		$reportRows[] = [
			$userId,
			$puserId,
			$loginEmail,
			$externalId,
			$needsLoginEmailUpdate,
			$needsExternalIdUpdate,
			$duplicateCount
		];
	}

	return writeArrayToCsv(
		$baseFilename,
		['userId', 'puserId', 'loginEmail', 'externalId', 'needsLoginEmailUpdate', 'needsExternalIdUpdate', 'duplicateEmailCount'],
		$reportRows
	);
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
function getUsersByPartnerAndCsv(int $partnerId, string $userListCsv = null): array
{

	if (empty($userListCsv)) {
		$noEmailUsers = getUsers($partnerId, false);
		$withEmailUsers = getUsers($partnerId, true);
	} else {
		$userList = file($userListCsv, FILE_IGNORE_NEW_LINES);
		$userListChunk = array_chunk($userList, 100);
		$noEmailUsers = [];
		$withEmailUsers = [];

		foreach ($userListChunk as $puserIds) {
			KalturaLog::log('Processing user IDs in chunk: [' . implode(',', $puserIds) . ']');
			$noEmailUsers = array_merge($noEmailUsers, getUsers($partnerId, false, $puserIds));
			$withEmailUsers = array_merge($withEmailUsers, getUsers($partnerId, true, $puserIds));
		}

		$kuserEmails = array_map(function ($user) {
			return $user->getEmail();
		}, $withEmailUsers);
	}
	return [
		'noEmailUsers' => $noEmailUsers,
		'withEmailUsers' => $withEmailUsers,
		'kuserEmails' => $kuserEmails ?? null
	];
}
