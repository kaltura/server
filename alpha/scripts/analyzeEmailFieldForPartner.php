<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
$script = basename($argv[0]);
$help = <<<HELP
Usage:
  php $script --partner-id=<partnerId> [--real-run] [--update-login-email] [--check-duplications]
			  [--set-puser-as-email-when-puser-is-email] [--metadata-profile-ids=<ids>]
			  [--user-list-csv=<path>] [--ignore-email-pattern=<regex>]... [--help]

Required options:
  --partner-id                        Partner ID to analyze.

Runtime options:
  --real-run                          Persist changes (default is dry run).
  --update-login-email                Update login emails for shared users to the expected format.
  --check-duplications                Collect duplicate email statistics.
  --set-puser-as-email-when-puser-is-email
									  When a user lacks an email but the puserId is a valid email, set it as the user's email and externalId.

Additional data options:
  --metadata-profile-ids=<ids>        Comma separated metadata profile IDs to enrich duplicate reporting with the KMS Roles.
  --user-list-csv=<path>              CSV file containing a 'puserId' column to scope processing otherwise it process the whole Partner users.
  --ignore-email-pattern=<regex>      Ignore users whose email (or related identifiers) matches the provided PCRE.
									  May be provided multiple times for different patterns.

Utility options:
  --help                              Display this help message.

Examples:
  php $script --partner-id=12345 --update-login-email --check-duplications
  php $script --partner-id=12345 --real-run --update-login-email --check-duplications --metadata-profile-ids=101,205 --user-list-csv=users.csv --ignore-email-pattern='/^registration-/i'

HELP;

$longOptions = [
	'partner-id:',
	'real-run',
	'update-login-email',
	'check-duplications',
	'set-puser-as-email-when-puser-is-email',
	'metadata-profile-ids:',
	'user-list-csv:',
	'ignore-email-pattern:',
	'help',
];

$options = getopt('', $longOptions, $optind);

$nonOptionArgs = array_slice($argv, $optind);
$shouldShowHelp = isset($options['help']);
$partnerIdOption = $options['partner-id'] ?? null;

if ($shouldShowHelp || $partnerIdOption === null) {
	fwrite(STDERR, $help);
	exit($shouldShowHelp ? 0 : 1);
}

if (!empty($nonOptionArgs)) {
	fwrite(STDERR, 'Unexpected arguments provided: ' . implode(' ', $nonOptionArgs) . PHP_EOL . PHP_EOL);
	fwrite(STDERR, $help);
	exit(1);
}

$partnerId = $partnerIdOption;
$dryRun = !isset($options['real-run']);
$updateLoginEmail = isset($options['update-login-email']);
$checkDuplications = isset($options['check-duplications']);
$setPuserAsEmailWhenPuserIsEmail = isset($options['set-puser-as-email-when-puser-is-email']);
$metadataProfileIds = [];
$userListCsv = $options['user-list-csv'] ?? '';
$metadataArg = $options['metadata-profile-ids'] ?? null;
$ignoreEmailPatternOption = $options['ignore-email-pattern'] ?? [];

if (!is_array($ignoreEmailPatternOption)) {
	$ignoreEmailPatternOption = [$ignoreEmailPatternOption];
}

if ($metadataArg !== null) {
	$metadataProfileIds = $metadataArg !== '' ? explode(',', $metadataArg) : [];
}

if (!$partner = PartnerPeer::retrieveByPK($partnerId)) {
	die("Please enter a valid partner Id!\n");
}

if (!empty($userListCsv) && !is_readable($userListCsv)) {
	die("User list file not found or not readable: $userListCsv\n");
}

$runModeLabel = $dryRun ? 'dryRun' : 'realRun';
KalturaLog::log('Starting to analyze users for partner [' . $partnerId . '] Run is a [' . $runModeLabel . '].' . (!empty($userListCsv) ? " Using user list from file [$userListCsv]." : ''));

$usersResult = getUsersByPartnerAndCsv($partnerId, $userListCsv);
$noEmailUsers = $usersResult['noEmailUsers'];
$withEmailUsers = $usersResult['withEmailUsers'];
$kuserEmailsFromListCsv = $usersResult['kuserEmailsFromListCsv'];
$emailExclusionPatterns = getAllUserExclusionPatterns($ignoreEmailPatternOption);

KalturaLog::log('Applying email exclusion patterns: ' . implode(', ', $emailExclusionPatterns));

$withEmailUsers = array_values(array_filter($withEmailUsers, function ($user) use ($emailExclusionPatterns) {
	$email = trim((string) $user->getEmail());
	return !shouldExcludeUserByPatterns($email, $emailExclusionPatterns);
}));

if (is_array($kuserEmailsFromListCsv)) {
	$kuserEmailsFromListCsv = array_values(array_filter($kuserEmailsFromListCsv, function ($email) use ($emailExclusionPatterns) {
		return !shouldExcludeUserByPatterns(trim((string) $email), $emailExclusionPatterns);
	}));
}

if (is_array($noEmailUsers)) {
	$noEmailUsers = array_values(array_filter($noEmailUsers, function ($user) use ($emailExclusionPatterns) {
		$emailFromPuserId = trim((string) $user->getPuserId());
		return !shouldExcludeUserByPatterns(trim((string) $emailFromPuserId), $emailExclusionPatterns);
	}));
}

$usersResultsEmailMap = buildEmailUsageMap($withEmailUsers);
$userCollectionSummary = summarizeUserCollections($withEmailUsers, $noEmailUsers);
$listByCSV = !empty($userListCsv);

$report = updateUserForSharedUsers($withEmailUsers, $dryRun, $partner->getAdminSecret(), $updateLoginEmail, $setPuserAsEmailWhenPuserIsEmail, $usersResultsEmailMap, $listByCSV);

KalturaLog::log('Users needing externalId update: ' . count($report['externalIdUpdates']));
KalturaLog::log('Users needing loginEmail update: ' . count($report['loginEmailUpdates']));

$duplicates = array();

if ($checkDuplications)
{
	KalturaLog::log('Checking for duplicated emails for partner [' . $partnerId . ']' . (!empty($userListCsv) ? ' By provided CSV List.' : '.'));
	$duplicates = countUsersWithDuplicatedEmail($partnerId, $kuserEmailsFromListCsv ?? null, $emailExclusionPatterns);
}


$userUpdateReportFile = prepareAndWriteUserUpdateReport($withEmailUsers,$noEmailUsers, $report, $duplicates, $partnerId, $metadataProfileIds, $checkDuplications, $emailExclusionPatterns, $usersResultsEmailMap);

$externalIdUpdatesCount = count($report['externalIdUpdates'] ?? []);
$loginEmailUpdatesCount = count($report['loginEmailUpdates'] ?? []);
$puserAsEmailUpdatesCount = count($report['puserAsEmailUpdates'] ?? []);
$duplicateSummary = $checkDuplications ? summarizeDuplicateData($duplicates, $usersResultsEmailMap) : ['duplicateEmailsCount' => 0, 'duplicateUsersCount' => 0];
$totalUsers = $userCollectionSummary['withEmailCount'] + $userCollectionSummary['noEmailCount'];
$noUserPercentage = noEmailPercentage($userCollectionSummary['noEmailCount'], $totalUsers);

KalturaLog::log("Summary for partner [$partnerId]:");
KalturaLog::log(" - Users with email: " . $userCollectionSummary['withEmailCount']);
KalturaLog::log("   - With valid email value: " . $userCollectionSummary['withEmailValidCount']);
KalturaLog::log("   - With empty string email: " . $userCollectionSummary['withEmailEmptyCount']);
KalturaLog::log(" - Users without email: " . $userCollectionSummary['noEmailCount']);
KalturaLog::log("   - Percentage without email: {$noUserPercentage}%");
KalturaLog::log(" - Unique users fetched: " . $userCollectionSummary['uniqueUsersCount']);
KalturaLog::log(" - Users with External ID updates: $externalIdUpdatesCount");
KalturaLog::log(" - Users with Login email updates: $loginEmailUpdatesCount");
KalturaLog::log(" - Users with Puser set as email: $puserAsEmailUpdatesCount");

if ($checkDuplications) {
	KalturaLog::log(" - Users with Duplicate emails detected: " . $duplicateSummary['duplicateEmailsCount']);
	KalturaLog::log(" - Users involved in duplicates: " . $duplicateSummary['duplicateUsersCount']);
} else {
	KalturaLog::log(' - Duplicate analysis skipped.');
}

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

function getUserByEmail($partnerId, $email): array {
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::IS_ADMIN, array(0,1), Criteria::IN);
	$emailCriteria->add(kuserPeer::EMAIL, $email,  Criteria::EQUAL);

	return kuserPeer::doSelect($emailCriteria);
}

/**
 * Placeholder for partner-specific patterns that should exclude users from processing and reporting.
 * Populate with PCRE patterns (e.g., '/^sys_/', '/^internal-/') as they become known.
 *
 * @param array<int,string> $additionalPatterns Additional PCRE patterns provided via CLI.
 * @return array<int,string>
 */
function getAllUserExclusionPatterns(array $additionalPatterns = []): array
{
	$patterns = [
		'/service\.kaltura\.com/',
		'/cncscp/',
		'/kmsSaaSAdmin/',
		'/WebcastingAdmin/',
		'/kmsAdminServiceUser/',
		'/__/',
		'/newrow-admin/',
		'/^connectors_framework/',
		'/^EPKalturaProxyDummyUser/',
	];

	if (empty($additionalPatterns)) {
		return $patterns;
	}

	$normalizedAdditionalPatterns = [];

	foreach ($additionalPatterns as $pattern) {
		$normalizedPattern = trim((string) $pattern);

		if ($normalizedPattern === '') {
			continue;
		}

		if (@preg_match($normalizedPattern, '') === false) {
			KalturaLog::warning('Skipping invalid exclusion pattern [' . $normalizedPattern . '].');
			continue;
		}

		$normalizedAdditionalPatterns[] = $normalizedPattern;
	}

	if (empty($normalizedAdditionalPatterns)) {
		return $patterns;
	}

	$mergedPatterns = array_merge($patterns, $normalizedAdditionalPatterns);

	return array_values(array_unique($mergedPatterns));
}

/**
 * Checks whether an email matches any of the provided exclusion patterns.
 *
 * @param string $email
 * @param array<int,string> $patterns
 * @return bool
 */
function shouldExcludeUserByPatterns(string $email, array $patterns): bool
{
	$email = trim($email);

	if ($email === '' || empty($patterns)) {
		return false;
	}

	foreach ($patterns as $pattern) {
		if (@preg_match($pattern, $email) === 1) {
			return true;
		}
	}

	return false;
}

/**
 * Counts duplicated user emails for a partner, optionally constrained to a provided email list.
 * Large email lists are processed in chunks to avoid oversized queries.
 *
 * @param int $partnerId
 * @param array<string>|string|null $kuserEmailsFromListCsv
 * @param array<int,string> $ignoreEmailPatterns
 * @return array
 */
function countUsersWithDuplicatedEmail($partnerId, $kuserEmailsFromListCsv = null, array $ignoreEmailPatterns = []): array {

	KalturaLog::log('Counting user with email duplications .');

	$duplicates = [];

	$emailChunks = [[]];

	if (!empty($kuserEmailsFromListCsv)) {
		$emailList = is_array($kuserEmailsFromListCsv) ? $kuserEmailsFromListCsv : array_map('trim', explode(',', (string) $kuserEmailsFromListCsv));
		$emailList = array_values(array_unique(array_filter($emailList, function ($value) use ($ignoreEmailPatterns) {

			if ($value === '') {
				return false;
			}

			return !shouldExcludeUserByPatterns(trim((string) $value), $ignoreEmailPatterns);
		})));

		if (empty($emailList)) {
			return [];
		}

		$chunkSize = 5000;
		$emailChunks = array_chunk($emailList, $chunkSize);
	}

	$totalChunks = count($emailChunks);

	foreach ($emailChunks as $index => $chunkEmails) {
		$originalChunkCount = count($chunkEmails);

		if (!empty($ignoreEmailPatterns) && !empty($chunkEmails)) {
			$chunkEmails = array_values(array_filter($chunkEmails, function ($email) use ($ignoreEmailPatterns) {
				return !shouldExcludeUserByPatterns(trim((string) $email), $ignoreEmailPatterns);
			}));
		}

		if (!empty($chunkEmails)) {
			KalturaLog::log('Processing duplicate email chunk [' . ($index + 1) . '/' . $totalChunks . '] containing [' . count($chunkEmails) . '] addresses.');
		}

		if ($originalChunkCount > 0 && empty($chunkEmails)) {
			continue;
		}

		$chunkDuplicates = fetchDuplicateUsersByEmails($partnerId, $chunkEmails, $ignoreEmailPatterns);

		foreach ($chunkDuplicates as $chunkDuplicate) {
			$duplicates[] = $chunkDuplicate;
		}
	}

	return $duplicates;
}

/**
 * Retrieves duplicate email information for an optional subset of emails.
 *
 * @param int $partnerId
 * @param array<int,string> $emailFilter
 * @param array<int,string> $ignoreEmailPatterns
 * @return array
 * @throws PropelException
 */
function fetchDuplicateUsersByEmails(int $partnerId, array $emailFilter = [], array $ignoreEmailPatterns = []): array
{
	$countField = 'COUNT(kuser.EMAIL)';
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::IS_ADMIN, array(0, 1), Criteria::IN);
	$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);

	if (!empty($emailFilter)) {
		$emailCriteria->add(kuserPeer::EMAIL, $emailFilter, Criteria::IN);
	}

	$emailCriteria->addGroupByColumn(kuserPeer::EMAIL);
	$emailCriteria->addSelectColumn($countField);
	$emailCriteria->addSelectColumn(kuserPeer::EMAIL);
	$emailCriteria->addHaving($emailCriteria->getNewCriterion(kuserPeer::EMAIL, $countField . '>' . 1, Criteria::CUSTOM));
	$stmt = kuserPeer::doSelectStmt($emailCriteria);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$duplicates = [];
	$duplicateEmails = [];
	$shouldIgnore = function (string $email) use ($ignoreEmailPatterns): bool {
		return shouldExcludeUserByPatterns($email, $ignoreEmailPatterns);
	};

	foreach ($rows as $row) {
		$emailValue = isset($row['EMAIL']) ? strtolower(trim((string) $row['EMAIL'])) : '';

		if ($emailValue === '') {
			KalturaLog::log('Skipping duplicate email aggregation for empty string value.');
			continue;
		}

		if ($shouldIgnore($emailValue)) {
			KalturaLog::log('Skipping duplicate email aggregation for excluded pattern value [' . $emailValue . '].');
			continue;
		}

		KalturaLog::log("email [" . $emailValue . "] is duplicated [" . $row['COUNT(kuser.EMAIL)'] . "] times");
		$duplicates[] = [
			$emailValue,
			$row['COUNT(kuser.EMAIL)']
		];

		$duplicateEmails[] = $emailValue;
	}

	if (empty($duplicateEmails)) {
		return $duplicates;
	}

	$chunkSize = 500;
	$baseCriteria = new Criteria();
	$baseCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$baseCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$baseCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$baseCriteria->add(kuserPeer::IS_ADMIN, array(0,1), Criteria::IN);

	foreach (array_chunk($duplicateEmails, $chunkSize) as $emailsChunk) {
		$duplicateUsersCriteria = clone $baseCriteria;
		$duplicateUsersCriteria->add(kuserPeer::EMAIL, $emailsChunk, Criteria::IN);

		$duplicateEmailUsers = kuserPeer::doSelect($duplicateUsersCriteria);
		kMemoryManager::clearMemory();

		foreach ($duplicateEmailUsers as $duplicateEmailUser) {
			$loginData = $duplicateEmailUser->getLoginData();
			$userEmail = trim((string) $duplicateEmailUser->getEmail());

			if ($userEmail === '') {
				continue;
			}

			if ($shouldIgnore($userEmail)) {
				KalturaLog::log('Skipping duplicate user aggregation for excluded pattern value [' . $userEmail . '].');
				continue;
			}

			$kuserId = $duplicateEmailUser->getId();

			$data = [
				'email' => $userEmail,
				'puserId' => $duplicateEmailUser->getPuserId(),
				'kuserId' => $kuserId,
				'loginEmail' => $loginData ? $loginData->getLoginEmail() : null,
				'firstName' => $duplicateEmailUser->getFirstName(),
				'lastName' => $duplicateEmailUser->getLastName(),
				'createdAt' => $duplicateEmailUser->getCreatedAt(),
				'externalId' => $duplicateEmailUser->getExternalId(),
				'isAdmin' => $duplicateEmailUser->getIsAdmin(),
				'status' => $duplicateEmailUser->getStatus(),
				'registrationInfo' => $duplicateEmailUser->getRegistrationInfo(),
				'isValidForEP' => isUserValidEP($duplicateEmailUser),
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
 * @param array $usersWithEmail
 * @param bool $dryRun
 * @param string $adminSecret
 * @param bool $updateLoginEmail
 * @param bool $setPuserAsEmailWhenPuserIsEmail
 * @param array $usersResultsEmailMap
 * @param bool $listByCSV
 *
 * @return array An associative array containing two lists of updated users:
 *               'externalIdUpdates' for users whose external ID was updated,
 *               'loginEmailUpdates' for users whose login email was updated.
 * @throws PropelException
 */
function updateUserForSharedUsers(array $usersWithEmail, bool $dryRun, string $adminSecret, bool $updateLoginEmail, bool $setPuserAsEmailWhenPuserIsEmail, array $usersResultsEmailMap, bool $listByCSV): array {
	KalturaLog::log('Processing users with email for updates.');

	$report = ['externalIdUpdates' => [], 'loginEmailUpdates' => [], 'puserAsEmailUpdates' => []];

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
						KalturaLog::log('Setting loginEmail for user [' . $user->getId() . '] to ' . $uniqueEmail);
						$loginData->setLoginEmail($uniqueEmail);
						$loginData->save();
					} else {
						KalturaLog::log('Dry RUN - would set loginEmail for user [' . $user->getId() . '] to ' . $uniqueEmail);
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

					if (!$listByCSV) {
						// When we are not listing by CSV, we can use the existing email map to check for existing users with this email, instead of fetching from DB each time.
						$emailKey = strtolower(trim((string)$user->getPuserId()));
						$existingUserWithPuserAsEmail = isset($usersResultsEmailMap[$emailKey]) && !isset($usersResultsEmailMap[$emailKey][$user->getId()]);
					} else {
						// When listing by CSV, we don't have the full email map, so we need to fetch from DB each time.
						$existingUserWithPuserAsEmail = getUserByEmail($user->getPartnerId(), $user->getPuserId());
					}

					if ($setPuserAsEmailWhenPuserIsEmail && empty($existingUserWithPuserAsEmail)) {
						$report['puserAsEmailUpdates'][] = $user->getId();
						$puserEmail = $user->getPuserId();
						KalturaLog::log('Setting puserId as email for user [' . $user->getId() . '] since puserId is a valid email and no other user has this email.');

						if (!$dryRun) {
							$user->setEmail($puserEmail);
							if (!$listByCSV) {
								$usersResultsEmailMap[$emailKey][$user->getId()] = true;
							}
						} else {
							KalturaLog::log('Dry RUN - would set email for user [' . $user->getId() . '] to ' . $puserEmail);
							if (!$listByCSV) {
								$usersResultsEmailMap[$emailKey][$user->getId()] = true;
							}
						}
					}

					$externalId = $user->getPuserId();
				}

				$report['externalIdUpdates'][] = $user->getId();

				if (!$dryRun) {
					KalturaLog::log('Copying email [' . $externalId . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
					$user->setExternalId($externalId);
					$user->save();
					kEventsManager::flushEvents();
				} else {
					KalturaLog::log('Dry RUN - would copy email [' . $externalId . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
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
	$headers = ['kuserId', 'email', 'puserId', 'loginEmail', 'FirstName', 'LastName', 'CreatedAt', 'externalId', 'isAdmin', 'needsLoginEmailUpdate', 'needsExternalIdUpdate', 'puserSetAsEmail', 'status', 'isValidForEP', 'regOrigin'];

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
 * @param array $emailUsageMap Map of normalized email => array of userIds (built from full user set) used to backfill counts.
 * @return array{
 *     emailCounts: array<string,int>,
 *     byKuserId: array<int,array>,
 *     userIds: array<int>
 * }
 */
function normalizeDuplicateData(array $duplicates, array $emailUsageMap = []): array
{
	$duplicateEmailCounts = [];
	$duplicatesByKuserId = [];
	$userDerivedEmailCounts = [];

	foreach ($duplicates as $dup) {
		if (is_array($dup) && isset($dup[0], $dup[1]) && is_string($dup[0]) && is_numeric($dup[1])) {
			$email = strtolower(trim((string) $dup[0]));
			$count = (int) $dup[1];

			if (!isset($duplicateEmailCounts[$email])) {
				$duplicateEmailCounts[$email] = 0;
			}

			$duplicateEmailCounts[$email] += $count;
			continue;
		}

		if (!is_array($dup) || !isset($dup['kuserId'])) {
			continue;
		}

		$kuserId = $dup['kuserId'];
		$duplicatesByKuserId[$kuserId] = $dup;

		$emailFromUser = strtolower(trim((string) ($dup['email'] ?? '')));

		if ($emailFromUser !== '') {
			if (!isset($userDerivedEmailCounts[$emailFromUser])) {
				$userDerivedEmailCounts[$emailFromUser] = 0;
			}

			$userDerivedEmailCounts[$emailFromUser]++;
		}
	}

	foreach ($userDerivedEmailCounts as $email => $count) {
		if (!isset($duplicateEmailCounts[$email]) || $duplicateEmailCounts[$email] < $count) {
			$duplicateEmailCounts[$email] = $count;
		}
	}

	foreach ($emailUsageMap as $email => $users) {
		$normalizedEmail = strtolower(trim((string) $email));

		if ($normalizedEmail === '') {
			continue;
		}

		$count = is_array($users) ? count($users) : 0;

		if ($count <= 1) {
			continue;
		}

		if (!isset($duplicateEmailCounts[$normalizedEmail]) || $duplicateEmailCounts[$normalizedEmail] < $count) {
			$duplicateEmailCounts[$normalizedEmail] = $count;
		}
	}

	return [
		'emailCounts' => $duplicateEmailCounts,
		'byKuserId' => $duplicatesByKuserId,
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
 * @param array $ownedEntryCountsByKuserId
 * @param array $metadataProfileIds
 * @param array $metadataRolesByUser
 * @param array $loginEmailUpdateIds
 * @param array $externalIdUpdateIds
 * @param array $puserAsEmailUpdateIds
 * @param bool $includeDuplicateColumns
 * @param int $partnerId
 * @return array{rows: array<int,array>, processedUserIds: array<int,bool>}
 */
function buildRowsForUsersWithEmail(array $withEmailUsers, array $duplicateEmailCounts, array $ownedEntryCountsByKuserId, array $metadataProfileIds, array $metadataRolesByUser, array $loginEmailUpdateIds, array $externalIdUpdateIds, array $puserAsEmailUpdateIds, bool $includeDuplicateColumns, int $partnerId): array
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
		$needsPuserAsEmailUpdate = isset($puserAsEmailUpdateIds[$kuserId]) ? 'yes' : 'no';
		$userEmail = $user->getEmail();
		$normalizedEmail = strtolower(trim((string) $userEmail));
		$duplicateCount = $duplicateEmailCounts[$normalizedEmail] ?? 0;
		$entryOwnedCount = '';
		$status = getUserStatusEnumLabel($user->getStatus());
		$isValidForEP = isUserValidEP($user);
		$regOrigin = extractRegOrigin($user->getAttendanceInfo());

		if ($includeDuplicateColumns) {
			$entryOwnedCount = $ownedEntryCountsByKuserId[$kuserId] ?? ($duplicateCount > 0 ? 0 : '');
		}

		$row = [$kuserId, $userEmail, $puserId, $loginEmail, $firstName, $lastName, $createdAt, $externalId, $isAdmin, $needsLoginEmailUpdate, $needsExternalIdUpdate, $needsPuserAsEmailUpdate, $status, $isValidForEP, $regOrigin];

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
 * Builds report rows for users without email information.
 *
 * @param array<int,kuser> $noEmailUsers
 * @param array<int,int> $metadataProfileIds
 * @param array<int,int> $puserAsEmailUpdateIds
 * @param bool $includeDuplicateColumns
 *
 * @return array{rows: array<int,array>, processedUserIds: array<int,bool>}
 */
function buildRowsForUsersWithoutEmail(array $noEmailUsers, array $metadataProfileIds, array $puserAsEmailUpdateIds, bool $includeDuplicateColumns): array
{
	$rows = [];
	$processedUserIds = [];

	foreach ($noEmailUsers as $user) {
		$kuserId = $user->getId();
		$processedUserIds[$kuserId] = true;

		$email = (string) $user->getEmail();
		$puserId = (string) $user->getPuserId();
		$loginEmail = $user->getLoginData() ? $user->getLoginData()->getLoginEmail() : '';
		$firstName = $user->getFirstName();
		$lastName = $user->getLastName();
		$createdAt = $user->getCreatedAt();
		$externalId = $user->getExternalId();
		$isAdmin = $user->getIsAdmin();
		$status = getUserStatusEnumLabel($user->getStatus());
		$puserAsEmail = isset($puserAsEmailUpdateIds[$kuserId]) ? 'yes' : 'no';
		$isValidForEP = isUserValidEP($user);
		$regOrigin = extractRegOrigin($user->getAttendanceInfo());

		$row = [$kuserId, $email, $puserId, $loginEmail, $firstName, $lastName, $createdAt, $externalId, $isAdmin, 'no', 'no', $puserAsEmail, $status, $isValidForEP, $regOrigin];

		if ($includeDuplicateColumns) {
			$row[] = '';
			$row[] = '';
		}

		if (!empty($metadataProfileIds)) {
			foreach ($metadataProfileIds as $profileId) {
				$row[] = 'not-checked';
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
 * @param array $ownedEntryCountsByKuserId
 * @param array $metadataProfileIds
 * @param array $metadataRolesByUser
 * @param array $loginEmailUpdateIds
 * @param array $externalIdUpdateIds
 * @param array $puserAsEmailUpdateIds
 * @param bool $includeDuplicateColumns
 * @return array
 */
function buildRowsForDuplicateOnlyUsers(array $duplicatesByKuserId, array $processedUserIds, array $duplicateEmailCounts, array $ownedEntryCountsByKuserId, array $metadataProfileIds, array $metadataRolesByUser, array $loginEmailUpdateIds, array $externalIdUpdateIds, array $puserAsEmailUpdateIds, bool $includeDuplicateColumns): array
{
	$rows = [];

	foreach ($duplicatesByKuserId as $dupUserId => $dup) {
		if (isset($processedUserIds[$dupUserId])) {
			continue;
		}

		$email = $dup['email'] ?? '';
		$normalizedEmail = strtolower(trim((string) $email));
		$duplicateCount = $normalizedEmail !== '' ? ($duplicateEmailCounts[$normalizedEmail] ?? 0) : 0;
		$regOrigin = extractRegOrigin($dup['registrationInfo'] ?? null);
		$status = getUserStatusEnumLabel($dup['status']);

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
			isset($puserAsEmailUpdateIds[$dupUserId]) ? 'yes' : 'no',
			$status,
			$dup['isValidForEP'] ?? '',
			$regOrigin,
		];

		if ($includeDuplicateColumns) {
			$row[] = $duplicateCount;
			$row[] = $ownedEntryCountsByKuserId[$dupUserId] ?? 0;
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
 * @param array $noEmailUsers An array of user objects without email information.
 * @param array $report An array containing 'loginEmailUpdates' and 'externalIdUpdates' mappings for users.
 * @param array $duplicates An array of duplicate records, where each record contains the email and its duplicate count.
 * @param int $partnerId Partner ID used when fetching metadata roles.
 * @param array $metadataProfileIds List of metadata profile IDs to include role columns for.
 * @param bool $includeDuplicateColumns Whether to append duplicate metrics columns.
 * @param array<int,string>|null $noEmailExclusionPatterns Patterns used to exclude users without emails.
 * @param array $emailUsageMap Map of normalized email => array of userIds (built from full user set) used to backfill counts.
 * @return string The filename of the generated report.
 * @throws Exception
 */
function prepareAndWriteUserUpdateReport(array $withEmailUsers, array $noEmailUsers, array $report, array $duplicates, int $partnerId, array $metadataProfileIds = [], bool $includeDuplicateColumns = true, ?array $noEmailExclusionPatterns = null, array $emailUsageMap = []): string
{
	$normalizedDuplicates = normalizeDuplicateData($duplicates, $emailUsageMap);
	$duplicateEmailCounts = $normalizedDuplicates['emailCounts'];
	$duplicatesByKuserId = $normalizedDuplicates['byKuserId'];
	$duplicateUserIds = $normalizedDuplicates['userIds'];

	$metadataResolution = resolveMetadataRoles($partnerId, $duplicateUserIds, $metadataProfileIds);
	$metadataRolesByUser = $metadataResolution['roles'];
	$metadataProfileIds = $metadataResolution['profileIds'];

	$ownedEntryCountsByKuserId = [];

	if ($includeDuplicateColumns) {
		$ownedEntryCounts = resolveOwnedEntryCounts($partnerId, $duplicatesByKuserId);
		$ownedEntryCountsByKuserId = $ownedEntryCounts['byKuserId'];
	}

	$headers = buildReportHeaders($includeDuplicateColumns, $metadataProfileIds);

	$loginEmailUpdateIds = array_flip($report['loginEmailUpdates'] ?? []);
	$externalIdUpdateIds = array_flip($report['externalIdUpdates'] ?? []);
	$puserAsEmailUpdateIds = array_flip($report['puserAsEmailUpdates'] ?? []);
	$usersWithEmailRows = buildRowsForUsersWithEmail($withEmailUsers, $duplicateEmailCounts, $ownedEntryCountsByKuserId, $metadataProfileIds, $metadataRolesByUser, $loginEmailUpdateIds, $externalIdUpdateIds, $puserAsEmailUpdateIds, $includeDuplicateColumns, $partnerId);
	$reportRows = $usersWithEmailRows['rows'];
	$processedUserIds = $usersWithEmailRows['processedUserIds'];

	$noEmailRows = buildRowsForUsersWithoutEmail($noEmailUsers, $metadataProfileIds, $puserAsEmailUpdateIds, $includeDuplicateColumns);
	$reportRows = array_merge($reportRows, $noEmailRows['rows']);
	$processedUserIds += $noEmailRows['processedUserIds'];

	$duplicateOnlyRows = buildRowsForDuplicateOnlyUsers($duplicatesByKuserId, $processedUserIds, $duplicateEmailCounts, $ownedEntryCountsByKuserId, $metadataProfileIds, $metadataRolesByUser, $loginEmailUpdateIds, $externalIdUpdateIds, $puserAsEmailUpdateIds, $includeDuplicateColumns);
	
	array_push($reportRows, ...$duplicateOnlyRows);

	return writeArrayToCsv($headers, $reportRows);
}

function summarizeDuplicateData(array $duplicates, array $emailUsageMap = []): array
{
	if (empty($duplicates)) {
		return [
			'duplicateEmailsCount' => 0,
			'duplicateUsersCount' => 0,
		];
	}

	$normalized = normalizeDuplicateData($duplicates, $emailUsageMap);

	return [
		'duplicateEmailsCount' => count($normalized['emailCounts']),
		'duplicateUsersCount' => count($normalized['byKuserId']),
	];
}

function summarizeUserCollections(array $withEmailUsers, array $noEmailUsers): array
{
	$withEmailCount = count($withEmailUsers);
	$withEmailEmptyCount = 0;
	$withEmailValidCount = 0;
	$uniqueUserIds = [];

	foreach ($withEmailUsers as $user) {
		$kuserId = (int) $user->getId();
		$uniqueUserIds[$kuserId] = true;

		$email = trim((string) $user->getEmail());
		if ($email === '') {
			$withEmailEmptyCount++;
		} else {
			$withEmailValidCount++;
		}
	}

	foreach ($noEmailUsers as $user) {
		$kuserId = (int) $user->getId();
		$uniqueUserIds[$kuserId] = true;
	}

	return [
		'withEmailCount' => $withEmailCount,
		'withEmailEmptyCount' => $withEmailEmptyCount,
		'withEmailValidCount' => $withEmailValidCount,
		'noEmailCount' => count($noEmailUsers),
		'uniqueUsersCount' => count($uniqueUserIds),
	];
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
 *               - 'kuserEmailsFromListCsv': Array of email addresses extracted from 'withEmailUsers', or null if no CSV file is provided.
 */
function getUsersByPartnerAndCsv(int $partnerId, string $userListCsv = ''): array {

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

		$kuserEmailsFromListCsv = array_map(function ($user) {
			return $user->getEmail();
		}, $withEmailUsers);
	}

	return ['noEmailUsers' => $noEmailUsers, 'withEmailUsers' => $withEmailUsers, 'kuserEmailsFromListCsv' => $kuserEmailsFromListCsv ?? null];
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

function resolveOwnedEntryCounts(int $partnerId, array $duplicatesByKuserId, int $chunkSize = 100): array {
	$ownedEntryCountsByKuserId = [];

	if (empty($duplicatesByKuserId)) {
		return ['byKuserId' => $ownedEntryCountsByKuserId];
	}

	$uniqueUserIds = array_values(array_unique(array_map('intval', array_keys($duplicatesByKuserId))));

	foreach (array_chunk($uniqueUserIds, $chunkSize) as $userIdsChunk) {
		$chunkResults = getOwnedEntryCounts($partnerId, $userIdsChunk);

		foreach ($userIdsChunk as $rawKuserId) {
			$kuserId = (int) $rawKuserId;
			$entryCount = $chunkResults[$kuserId] ?? 0;
			$ownedEntryCountsByKuserId[$kuserId] = (int) $entryCount;
		}
	}

	return ['byKuserId' => $ownedEntryCountsByKuserId];
}

function getOwnedEntryCounts(int $partnerId, array $kuserIds): array {
	if (empty($kuserIds)) {
		return [];
	}

	$criteria = new Criteria();
	$criteria->add(entryPeer::PARTNER_ID, $partnerId);
	$criteria->add(entryPeer::KUSER_ID, $kuserIds, Criteria::IN);
	$criteria->add(entryPeer::STATUS, 2);
	$criteria->clearSelectColumns();
	$criteria->addAsColumn('entry_owner_kuser_id', entryPeer::KUSER_ID);
	$criteria->addAsColumn('entry_count', 'COUNT(*)');
	$criteria->addGroupByColumn(entryPeer::KUSER_ID);
	$stmt = entryPeer::doSelectStmt($criteria);

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = [];

	foreach ($rows as $row) {
		$kuserId = isset($row['entry_owner_kuser_id']) ? (int) $row['entry_owner_kuser_id'] : null;

		if ($kuserId === null) {
			continue;
		}

		$results[$kuserId] = (int) ($row['entry_count'] ?? 0);
	}

	return $results;
}

function buildEmailUsageMap(array $withEmailUsers): array {
	$usage = [];

	foreach ($withEmailUsers as $user) {
		$email = strtolower(trim((string) $user->getEmail()));

		if ($email === '') {
			continue;
		}

		$usage[$email][$user->getId()] = true;
	}

	return $usage;
}

function getUserStatusEnumLabel($status): string {

	$switchVal = is_numeric($status) ? (int) $status : $status;

	switch ($switchVal) {
		case KuserStatus::BLOCKED:
			// BLOCKED = 0;
			return 'Blocked';

		case KuserStatus::ACTIVE:
			// ACTIVE = 1;
			return 'Active';

		case KuserStatus::DELETED:
			// DELETED = 2;
			return 'Deleted';

		default:
			return $status;
	}
}

function isUserValidEP(Kuser $user): string {
	$puserId = $user->getPuserId();
	$email = $user->getEmail();
	$isValidForEP = null;
	$existingFirstName = strtolower(trim($user->getFirstName()));
	$existingLastName = strtolower(trim($user->getLastName()));

	if (empty($email)) {
		KalturaLog::log('User [' . $puserId . '] is missing Email.');
		return 'false';
	}

	if (empty($existingFirstName)) {
		KalturaLog::log('User [' . $puserId . '] is missing first name.');
		$isValidForEP = false;
	} 

	if (empty($existingLastName)) {
		KalturaLog::log('User [' . $puserId . '] is missing last name.');
		$isValidForEP = false;
	}

	return $isValidForEP === false ? 'false' : 'true';
}

/**
 * Safely extracts regOrigin from attendance/registration info that may be JSON, array, or object.
 */
function extractRegOrigin($attendanceInfo)
{
	if (is_string($attendanceInfo)) {
		$decoded = json_decode($attendanceInfo, true);

		if (json_last_error() === JSON_ERROR_NONE) {
			$attendanceInfo = $decoded;
		}
	}

	if (is_array($attendanceInfo)) {
		return $attendanceInfo['regOrigin'] ?? null;
	}

	if (is_object($attendanceInfo) && property_exists($attendanceInfo, 'regOrigin')) {
		return $attendanceInfo->regOrigin;
	}

	return null;
}
