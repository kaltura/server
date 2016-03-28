<?php
/**
 * Enable question cue point objects and answer cue point objects management on entry objects
 * @package plugins.ask
 */
class AskPlugin extends KalturaPlugin implements IKalturaCuePoint, IKalturaServices, IKalturaDynamicAttributesContributer, IKalturaEventConsumers, IKalturaReportProvider, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'ask';

	const CUE_POINT_VERSION_MAJOR = 1;
	const CUE_POINT_VERSION_MINOR = 0;
	const CUE_POINT_VERSION_BUILD = 0;
	const CUE_POINT_NAME = 'cuePoint';

	const ANSWERS_OPTIONS = "answersOptions";
	const ASK_MANAGER = "kAskManager";
	const IS_ASK = "isAsk";
	const ASK_DATA = "askData";
	
	const SEARCH_TEXT_SUFFIX = 'qend';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap ()
	{
		$map = array(
			'ask' => 'AskService',
			'askUserEntry' => 'AskUserEntryService'
		);
		return $map;
	}


	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('AskCuePointType','AskUserEntryType',"AskUserEntryStatus","AskEntryCapability","AskReportType");
		if ($baseEnumName == 'CuePointType')
			return array('AskCuePointType');
		if ($baseEnumName == "UserEntryType")
		{
			return array("AskUserEntryType");
		}
		if ($baseEnumName == "UserEntryStatus")
		{
			return array("AskUserEntryStatus");
		}
		if ($baseEnumName == 'EntryCapability')
		{
			return array("AskEntryCapability");
		}
		if ($baseEnumName == 'ReportType')
		{
			return array("AskReportType");
		}


		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$cuePointVersion = new KalturaVersion(
			self::CUE_POINT_VERSION_MAJOR,
			self::CUE_POINT_VERSION_MINOR,
			self::CUE_POINT_VERSION_BUILD);

		$dependency = new KalturaDependency(self::CUE_POINT_NAME, $cuePointVersion);
		return array($dependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaCuePoint') {
			if ( $enumValue == self::getCuePointTypeCoreValue(AskCuePointType::ASK_QUESTION))
				return new KalturaQuestionCuePoint();

			if ( $enumValue == self::getCuePointTypeCoreValue(AskCuePointType::ASK_ANSWER))
				return new KalturaAnswerCuePoint();
		}
		if ( ($baseClass=="KalturaUserEntry") && ($enumValue ==  self::getCoreValue('UserEntryType' , AskUserEntryType::ASK)))
		{
			return new KalturaAskUserEntry();
		}
		if ( ($baseClass=="UserEntry") && ($enumValue == self::getCoreValue('UserEntryType' , AskUserEntryType::ASK)))
		{
			return new AskUserEntry();
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint') {
			if ($enumValue == self::getCuePointTypeCoreValue(AskCuePointType::ASK_QUESTION))
				return 'QuestionCuePoint';
			if ($enumValue == self::getCuePointTypeCoreValue(AskCuePointType::ASK_ANSWER))
				return 'AnswerCuePoint';
		}
		if ($baseClass == 'UserEntry' && $enumValue == self::getCoreValue('UserEntryType' , AskUserEntryType::ASK))
		{
			return AskUserEntry::ASK_OM_CLASS;
		}

	}

	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::ASK_MANAGER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != SchemaType::SYNDICATION
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
			return null;

		$xsd = '

		<!-- ' . self::getPluginName() . ' -->

		<xs:complexType name="T_scene_questionCuePoint">
			<xs:complexContent>
				<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="question" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="hint" minOccurs="0" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="explanation" minOccurs="0" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="optionalAnswers" minOccurs="0" maxOccurs="1" type="KalturaOptionalAnswersArray"></xs:element>
					<xs:element name="correctAnswerKeys" minOccurs="0" maxOccurs="1" type="KalturaStringArray"></xs:element>
				</xs:sequence>
				</xs:extension>
			</xs:complexContent>
		</xs:complexType>

		<xs:element name="scene-question-cue-point" type="T_scene_questionCuePoint" substitutionGroup="scene">
			<xs:annotation>
				<xs:documentation>Single question cue point element</xs:documentation>
				<xs:appinfo>
					<example>
						<scene-question-cue-point sceneId="{scene id}" entryId="{entry id}">
							<sceneStartTime>00:00:05.3</sceneStartTime>
							<tags>
								<tag>my_tag</tag>
							</tags>
						</scene-question-cue-point>
					</example>
				</xs:appinfo>
			</xs:annotation>
		</xs:element>

		<xs:complexType name="T_scene_answerCuePoint">
			<xs:complexContent>
				<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="answerKey" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="askUserEntryId" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="parentId" minOccurs="1" maxOccurs="1" type="xs:string">
						<xs:annotation>
							<xs:documentation>ID of the parent questionCuePoint</xs:documentation>
						</xs:annotation>
					</xs:element>
				</xs:sequence>
				</xs:extension>
			</xs:complexContent>
		</xs:complexType>

		<xs:element name="scene-answer-cue-point" type="T_scene_answerCuePoint" substitutionGroup="scene">
			<xs:annotation>
				<xs:documentation>Single answer cue point element</xs:documentation>
				<xs:appinfo>
					<example>
						<scene-answer-cue-point sceneId="{scene id}" entryId="{entry id}">
							<sceneStartTime>00:00:05.3</sceneStartTime>
							<tags>
								<tag>my_tag</tag>
							</tags>
						</scene-answer-cue-point>
					</example>
				</xs:appinfo>
			</xs:annotation>
		</xs:element>

		';
		return $xsd;
	}

	/* (non-PHPdoc)
 	* @see IKalturaCuePoint::getCuePointTypeCoreValue()
 	*/
	public static function getCuePointTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('CuePointType', $value);
	}

	public static  function getCapatabilityCoreValue()
	{
		return kPluginableEnumsManager::apiToCore('EntryCapability', self::PLUGIN_NAME . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . self::PLUGIN_NAME);
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCoreValue($type, $valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore($type, $value);
	}

	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getApiValue()
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getTypesToIndexOnEntry()
	*/
	public static function getTypesToIndexOnEntry()
	{
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaDynamicAttributesContributer::getDynamicAttribute()
	 */
	public static function getDynamicAttributes(IIndexable $object)
	{
		if ( $object instanceof entry ) {
			if ( !is_null($object->getFromCustomData(self::ASK_DATA)) )
			{
				return array(self::getDynamicAttributeName() => 1);
			}
		}

		return array();
	}

	public static function getDynamicAttributeName()
	{
		return self::getPluginName() . '_' . self::IS_ASK;
	}

	/**
	 * @param entry $entry
	 * @return kAsk
	 */
	public static function getAskData( entry $entry )
	{
		$askData = $entry->getFromCustomData( self::ASK_DATA );
		return $askData;
	}

	/**
	 * @param entry $entry
	 * @param kAsk $kAsk
	 */
	public static function setAskData( entry $entry, kAsk $kAsk )
	{
		$entry->putInCustomData( self::ASK_DATA, $kAsk);
		$entry->addCapability(self::getCapatabilityCoreValue());
	}

	/**
	 * @param entry $dbEntry
	 * @return mixed|string
	 * @throws Exception
	 */
	public static function validateAndGetAsk( entry $dbEntry ) {
		$kAsk = self::getAskData($dbEntry);
		if ( !$kAsk )
			throw new kCoreException("Entry is not a ask",kCoreException::INVALID_ENTRY_ID, $dbEntry->getId());

		return $kAsk;
	}


	/**
	 * @param string $partner_id
	 * @param AskReportType $report_type
	 * @param AskReportType $report_flavor
	 * @param string $objectIds
	 * @param $inputFilter
	 * @param $page_size
	 * @param $page_index
	 * @param null $orderBy
	 * @return array|null
	 * @throws kCoreException
	 */
	public function getReportResult($partner_id, $report_type, $report_flavor, $objectIds, $inputFilter,
									$page_size , $page_index, $orderBy = null)
	{
		$ans = array();
		if (!in_array(str_replace(self::getPluginName().".", "", $report_type), AskReportType::getAdditionalValues()))
		{
			return null;
		}
		switch ($report_flavor)
		{
			case myReportsMgr::REPORT_FLAVOR_TOTAL:
				return $this->getTotalReport($objectIds);
			case myReportsMgr::REPORT_FLAVOR_TABLE:
				if ($report_type == (self::getPluginName() . "." . AskReportType::ASK))
				{
					$ans = $this->getQuestionPercentageTableReport($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_USER_PERCENTAGE))
				{
					$ans = $this->getUserPercentageTable($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_AGGREGATE_BY_QUESTION))
				{
					$ans = $this->getAskQuestionPercentageTableReport($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_USER_AGGREGATE_BY_QUESTION))
				{
					$ans = $this->getUserPrecentageByUserAndEntryTable($objectIds, $inputFilter, $orderBy);
				}
				return $this->pagerResults($ans, $page_size , $page_index);

			case myReportsMgr::REPORT_FLAVOR_COUNT:
				if ($report_type == (self::getPluginName() . "." . AskReportType::ASK))
				{
					return $this->getReportCount($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_USER_PERCENTAGE) )
				{
					return $this->getUserPercentageCount($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_AGGREGATE_BY_QUESTION))
				{
					return $this->getQuestionCountByQusetionIds($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . AskReportType::ASK_USER_AGGREGATE_BY_QUESTION))
				{
					return $this->getAnswerCountByUserIdsAndEntryIds($objectIds, $inputFilter);
				}
			default:
				return null;
		}
	}

	/**
	 * The method returns only part of the results according to the $page_size and $page_index parameters
	 * $ans - array of all the answers
	 * $page_size - The number of entries that should be displyed on the screen
	 * $page_index - The index pf the first entry that will be displayed on the screen
	 * @param $ans
	 * @param $page_size
	 * @param $page_index
	 * @return array
	 */
	protected function pagerResults(array $ans, $page_size , $page_index)
	{
		KalturaLog::debug("ASK Report::: page_size [$page_size] page_index [$page_index] array size [" .count($ans)."]");
		$res = array();
		if ($page_index ==0)
			$page_index = 1;

		if ($page_index * $page_size > count($ans))
		{
			return $ans;
		}

		$indexInArray = ($page_index -1) * $page_size;
		$res = array_slice($ans, $indexInArray, $page_size, false );
		KalturaLog::debug("ASK Report::: The number of arguments in the response is [" .count($res)."]");
		return $res;
	}

	/**
	 * @param $objectIds
	 * @return array
	 * @throws kCoreException
	 */
	protected function getTotalReport($objectIds)
	{
		if (!$objectIds)
		{
			throw new kCoreException("",kCoreException::INVALID_ENTRY_ID, $objectIds);
		}
		$avg = 0;
		$dbEntry = entryPeer::retrieveByPK($objectIds);
		if (!$dbEntry)
			throw new kCoreException("",kCoreException::INVALID_ENTRY_ID, $objectIds);
		$kAsk = self::getAskData($dbEntry);
		if ( !$kAsk )
			return array(array('average' => null));
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $objectIds);
		$c->add(UserEntryPeer::TYPE, AskPlugin::getCoreValue('UserEntryType', AskUserEntryType::ASK));
		$c->add(UserEntryPeer::STATUS, AskPlugin::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED));

		$askzes = UserEntryPeer::doSelect($c);
		$numOfAskzesFound = count($askzes);
		KalturaLog::debug("Found $numOfAskzesFound askzes that were submitted");
		if ($numOfAskzesFound)
		{
			$sumOfScores = 0;
			foreach ($askzes as $ask)
			{
				/**
				 * @var AskUserEntry $ask
				 */
				$sumOfScores += $ask->getScore();
			}
			$avg = $sumOfScores / $numOfAskzesFound;
		}
		return array(array('average' => $avg));
	}

	/**
	 * @param $objectIds
	 * @return array
	 * @throws kCoreException
	 */
	protected function getQuestionPercentageTableReport($objectIds, $orderBy)
	{
		$dbEntry = entryPeer::retrieveByPK($objectIds);
		if (!$dbEntry)
			throw new kCoreException("",kCoreException::INVALID_ENTRY_ID, $objectIds);
		$kAsk = AskPlugin::validateAndGetAsk( $dbEntry );
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType',AskCuePointType::ASK_QUESTION));
		$questions = CuePointPeer::doSelect($c);
		return $this->getAggregateDataForQuestions($questions, $orderBy);
	}

	
	protected function getAskQuestionPercentageTableReport($objectIds, $orderBy)
	{
		$questionIds = baseObjectUtils::getObjectIdsAsArray($objectIds);
		$questionsCriteria = new Criteria();
		$questionsCriteria->add(CuePointPeer::ID, $questionIds, Criteria::IN);
		$questionsCriteria->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType',AskCuePointType::ASK_QUESTION));
		$questions = CuePointPeer::doSelect($questionsCriteria);

		return $this->getAggregateDataForQuestions($questions, $orderBy);
	}
	
	
	/**
	 * @param $objectIds
	 * @return array
	 * @throws kCoreException
	 */
	protected function getReportCount($objectIds)
	{
		$dbEntry = entryPeer::retrieveByPK($objectIds);
		if (!$dbEntry)
		{
			throw new kCoreException("", kCoreException::INVALID_ENTRY_ID, $objectIds);
		}
		/**
		 * @var kAsk $kAsk
		 */
		$kAsk = AskPlugin::validateAndGetAsk($dbEntry);
		$ans = array();
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType', AskCuePointType::ASK_QUESTION));
		$anonKuserIds = $this->getAnonymousKuserIds($dbEntry->getPartnerId());
		if (!empty($anonKuserIds))
		{
			$c->add(CuePointPeer::KUSER_ID, $anonKuserIds, Criteria::NOT_IN);
		}

		$numOfquestions = CuePointPeer::doCount($c);
		$res = array();
		$res['count_all'] = $numOfquestions;
		return array($res);
	}

	protected function getUserPercentageCount($objectIds)
	{
		$c = new Criteria();
		$c->setDistinct();
		$c->addSelectColumn(UserEntryPeer::KUSER_ID);
		$c->add(UserEntryPeer::ENTRY_ID, $objectIds);
		$c->add(UserEntryPeer::STATUS, AskPlugin::getCoreValue('UserEntryStatus',AskUserEntryStatus::ASK_SUBMITTED));

		// if a user has answered the test twice (consider anonymous users) it will be calculated twice.
		$count = UserEntryPeer::doCount($c);

		$res = array();
		$res['count_all'] = $count;
		return array($res);
	}

	protected function getQuestionCountByQusetionIds($objectIds)
	{
		$questionIds = baseObjectUtils::getObjectIdsAsArray($objectIds);
		$c = new Criteria();
		$c->add(CuePointPeer::ID, $questionIds, Criteria::IN);
		$numOfquestions = CuePointPeer::doCount($c);
		$res = array();
		$res['count_all'] = $numOfquestions;
		return array($res);
	}

	private function getUserIdsFromFilter($inputFilter){
		if ($inputFilter instanceof endUserReportsInputFilter &&
			isset($inputFilter->userIds)){
			return $inputFilter->userIds ;
		}
		return null;
	}

	private static function isWithoutValue($text){
		return is_null($text) || $text === "";
	}



	protected function getAnswerCountByUserIdsAndEntryIds($entryIds, $inputFilter)
	{
		$userIds = $this->getUserIdsFromFilter($inputFilter);
		$c = new Criteria();
		if (!AskPlugin::isWithoutValue($userIds)) {
			$c = $this->createGetCuePointByUserIdsCriteria($userIds, $c);
		}
		if (!AskPlugin::isWithoutValue($entryIds)) {
			$c->add(CuePointPeer::ENTRY_ID, explode(",", $entryIds), Criteria::IN);
		}
		$c->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType', AskCuePointType::ASK_ANSWER));
		$numOfAnswers = 0;
		$answers = CuePointPeer::doSelect($c);
		foreach ($answers as $answer)
		{
			/**
			 * @var AnswerCuePoint $answer
			 */
			$askUserEntryId = $answer->getAskUserEntryId();
			if ($this->isAskUserEntrySubmitted($askUserEntryId))
			{
				$numOfAnswers++;
			}
		}
		$res['count_all'] = $numOfAnswers;
		return array($res);
	}
	
	/**
	 * @param $objectIds
	 * @return array
	 * @throws kCoreException
	 * @throws KalturaAPIException
	 */
	protected function getUserPercentageTable($objectIds, $orderBy)
	{
		$dbEntry = entryPeer::retrieveByPK($objectIds);
		if (!$dbEntry)
			throw new kCoreException("",kCoreException::INVALID_ENTRY_ID, $objectIds);
		/**
		 * @var kAsk $kAsk
		 */
		$kAsk = AskPlugin::validateAndGetAsk( $dbEntry );
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $objectIds);
		$userEntries = UserEntryPeer::doSelect($c);
		return $this->getAggregateDataForUsers($userEntries, $orderBy);
	}

	protected function getAggregateDataForUsers( $userEntries, $orderBy )
	{
		$ans = array();
		$usersCorrectAnswers = array();
		$usersTotalQuestions = array();
		foreach ($userEntries as $userEntry)
		{
			if ($userEntry->getStatus() == self::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED)) {
				if (isset($usersCorrectAnswers[$userEntry->getKuserId()]))
				{
					$usersCorrectAnswers[$userEntry->getKuserId()]+=$userEntry->getNumOfCorrectAnswers();
				} else
				{
					$usersCorrectAnswers[$userEntry->getKuserId()] = $userEntry->getNumOfCorrectAnswers();
				}
				if (isset($usersTotalQuestions[$userEntry->getKuserId()]))
				{
					$usersTotalQuestions[$userEntry->getKuserId()]+=$userEntry->getNumOfQuestions();
				} else
				{
					$usersTotalQuestions[$userEntry->getKuserId()] = $userEntry->getNumOfQuestions();
				}
			}
		}

		foreach (array_keys($usersTotalQuestions) as $kuserId)
		{
			$totalCorrect = 0;
			$totalAnswers = $usersTotalQuestions[$kuserId];
			if (isset($usersCorrectAnswers[$kuserId]))
			{
				$totalCorrect = $usersCorrectAnswers[$kuserId];
			}

			$userId = "Unknown";
			$dbKuser = kuserPeer::retrieveByPK($kuserId);
			if ($dbKuser)
			{
				if($dbKuser->getPuserId())
				{
					$userId = $dbKuser->getPuserId();
				}
			}
			$ans[$userId] = array('user_id' => $userId,
				'percentage' => ($totalCorrect / $totalAnswers) * 100,
				'num_of_correct_answers' => $totalCorrect,
				'num_of_wrong_answers' => ($totalAnswers - $totalCorrect));
		}

		uasort($ans, $this->getSortFunction($orderBy));
		$ans = array_values($ans);
		return $ans;
	}
	
	protected function getUserPrecentageByUserAndEntryTable($entryIds, $inputFilter, $orderBy)
	{
		$userIds = $this->getUserIdsFromFilter($inputFilter);
		$noEntryIds =  AskPlugin::isWithoutValue($entryIds);
		$noUserIds = AskPlugin::isWithoutValue($userIds);
		if ( $noEntryIds && $noUserIds){
			return array();
		}

		$c = new Criteria();
		if (!$noUserIds){
			$c->add(UserEntryPeer::KUSER_ID, $this->getKuserIds($userIds), Criteria::IN);
		}
		if (!$noEntryIds){
			$entryIdsArray = explode(",", $entryIds);
			$dbEntries = entryPeer::retrieveByPKs($entryIdsArray);
			if (empty($dbEntries)) {
				throw new kCoreException("", kCoreException::INVALID_ENTRY_ID, $entryIds);
			}
			$c->add(UserEntryPeer::ENTRY_ID, $entryIdsArray, Criteria::IN);
			$hasAnonymous = false;
			foreach ($dbEntries as $dbEntry) {
				$anonKuserIds = $this->getAnonymousKuserIds($dbEntry->getPartnerId());
				if (!empty($anonKuserIds)) {
					$hasAnonymous = true;
					break;
				}
			}
			if ($hasAnonymous) {
				$c->addAnd(UserEntryPeer::KUSER_ID, $anonKuserIds, Criteria::NOT_IN);
			}
		}
		$userEntries = UserEntryPeer::doSelect($c);
		return $this->getAggregateDataForUsers($userEntries, $orderBy);
	}

	private function getSortFunction($orderBy)
	{
		if (!$orderBy)
		{
			return null;
		}
		
		switch ($orderBy)
		{
			case '+percentage':
				return 'copmareNumbersDescending';
			case '-percentage':
				return 'copmareNumbersAscending';
			default:
				return 'copmareNumbersDescending';
		}
	}

	/**
	 * @param $objectIds
	 * @param $orderBy
	 * @param $questions
	 * @param $ans
	 * @return array
	 */
	protected function getAggregateDataForQuestions($questions, $orderBy)
	{
		$ans = array();
		foreach ($questions as $question)
		{
			$numOfCorrectAnswers = 0;
			/**
			 * @var QuestionCuePoint $question
			 */
			$c = new Criteria();
			$c->add(CuePointPeer::ENTRY_ID, $question->getEntryId());
			$c->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType', AskCuePointType::ASK_ANSWER));
			$c->add(CuePointPeer::PARENT_ID, $question->getId());
			$anonKuserIds = $this->getAnonymousKuserIds($question->getPartnerId());
			if (!empty($anonKuserIds))
			{
				$c->add(CuePointPeer::KUSER_ID, $anonKuserIds, Criteria::NOT_IN);
			}
			$answers = CuePointPeer::doSelect($c);
			$numOfAnswers = 0;
			foreach ($answers as $answer)
			{
				/**
				 * @var AnswerCuePoint $answer
				 */
				$askUserEntryId = $answer->getAskUserEntryId();
				if ($this->isAskUserEntrySubmitted($askUserEntryId))
				{
					$numOfAnswers++;
					$optionalAnswers = $question->getOptionalAnswers();
					$correct = false;
					foreach ($optionalAnswers as $optionalAnswer)
					{
						/**
						 * @var kOptionalAnswer $optionalAnswer
						 */
						if ($optionalAnswer->getKey() === $answer->getAnswerKey())
						{
							if ($optionalAnswer->getIsCorrect())
							{
								$numOfCorrectAnswers++;
								break;
							}
						}
					}
				}
			}
			if ($numOfAnswers)
			{
				$pctg = $numOfCorrectAnswers / $numOfAnswers;
			}
			else
			{
				$pctg = 0.0;
			}
			$ans[] = array('question_id' => $question->getId(), 
				'percentage' => $pctg * 100, 
				'num_of_correct_answers' => $numOfCorrectAnswers,
				'num_of_wrong_answers' => ($numOfAnswers - $numOfCorrectAnswers));
		}

		uasort($ans, $this->getSortFunction($orderBy));
		return $ans;
	}

	/**
	 * @param $objectIds
	 * @param $c criteria
	 * @return criteria
	 */
	protected function createGetCuePointByUserIdsCriteria($objectIds, $c)
	{
		$kuserIds = $this->getKuserIds($objectIds);
		$c->add(CuePointPeer::KUSER_ID, $kuserIds, Criteria::IN);
		return $c;
	}

	protected function getKuserIds($objectIds)
	{
		$userIds = baseObjectUtils::getObjectIdsAsArray($objectIds);
		$kuserIds = array();
		foreach ($userIds as $userId)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
			if ($kuser)
			{
				$kuserIds[] = $kuser->getKuserId();
			}
		}
		return $kuserIds;
	}
	
	//TODO: When cuePoints will be indexed in the sphinx we won't need this anymore since we'll be able to query the shpinx for this info
	protected function isAskUserEntrySubmitted($askUserEntryId)
	{
		$ans = false;
		$askUserEntry = UserEntryPeer::retrieveByPK($askUserEntryId);
		if ($askUserEntry)
		{
			if ($askUserEntry->getStatus() == self::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED))
			{
				$ans = true;
			}
		}
		return $ans;
	}

	/**
	 * @param $partnerID
	 */
	protected function getAnonymousKuserIds($partnerID)
	{
	    $anonKuserIds = array();
		$anonKusers = kuserPeer::getKuserByPartnerAndUids($partnerID, array('', 0));
		foreach ($anonKusers as $anonKuser) {
		    $anonKuserIds[] = $anonKuser->getKuserId();
		}
		return $anonKuserIds;
	}

	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
	    if ($object instanceof AnswerCuePoint)
	        return self::getAnswerCuePointSearchData($object);
	
	    return null;
	}
	
	public static function getAnswerCuePointSearchData(AnswerCuePoint $answerCuePoint)
	{
	    $data = $answerCuePoint->getAskUserEntryId();
	    return array(
	        'plugins_data' => AskPlugin::PLUGIN_NAME . ' ' . $data . $answerCuePoint->getPartnerId() . AskPlugin::SEARCH_TEXT_SUFFIX
	    );
	}
    public static function shouldCloneByProperty(entry $entry)
    {
        return false;
    }

}

function copmareNumbersAscending($a,$b)
{
	return innerCompare($a,$b)*(-1);
}

function copmareNumbersDescending($a,$b)
{
    return innerCompare($a,$b);
}

function innerCompare($a, $b)
{
	if (!isset($a['percentage']) || !isset($b['percentage']))
	{
		return 0;
	}
	$prctgA = $a['percentage'];
	$prctgB = $b['percentage'];
	if ($prctgA == $prctgB) {
		return 0;
	}
	return ($prctgA < $prctgB) ? -1 : 1;
}

