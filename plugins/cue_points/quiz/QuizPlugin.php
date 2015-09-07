<?php
/**
 * Enable question cue point objects and answer cue point objects management on entry objects
 * @package plugins.quiz
 */
class QuizPlugin extends KalturaPlugin implements IKalturaCuePoint, IKalturaServices, IKalturaDynamicAttributesContributer, IKalturaEventConsumers, IKalturaReportProvider
{
	const PLUGIN_NAME = 'quiz';

	const CUE_POINT_VERSION_MAJOR = 1;
	const CUE_POINT_VERSION_MINOR = 0;
	const CUE_POINT_VERSION_BUILD = 0;
	const CUE_POINT_NAME = 'cuePoint';

	const ANSWERS_OPTIONS = "answersOptions";
	const QUIZ_MANAGER = "kQuizManager";
	const IS_QUIZ = "isQuiz";
	const QUIZ_DATA = "quizData";

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
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap ()
	{
		$map = array(
			'quiz' => 'QuizService',
			'quizUserEntry' => 'QuizUserEntryService'
		);
		return $map;
	}


	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('QuizCuePointType','QuizUserEntryType',"QuizUserEntryStatus","QuizEntryCapability","QuizReportType");
		if ($baseEnumName == 'CuePointType')
			return array('QuizCuePointType');
		if ($baseEnumName == "UserEntryType")
		{
			return array("QuizUserEntryType");
		}
		if ($baseEnumName == "UserEntryStatus")
		{
			return array("QuizUserEntryStatus");
		}
		if ($baseEnumName == 'EntryCapability')
		{
			return array("QuizEntryCapability");
		}
		if ($baseEnumName == 'ReportType')
		{
			return array("QuizReportType");
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
			if ( $enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION))
				return new KalturaQuestionCuePoint();

			if ( $enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_ANSWER))
				return new KalturaAnswerCuePoint();
		}
		if ( ($baseClass=="KalturaUserEntry") && ($enumValue ==  self::getCoreValue('UserEntryType' , QuizUserEntryType::QUIZ)))
		{
			return new KalturaQuizUserEntry();
		}
		if ( ($baseClass=="UserEntry") && ($enumValue == self::getCoreValue('UserEntryType' , QuizUserEntryType::QUIZ)))
		{
			return new QuizUserEntry();
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint') {
			if ($enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION))
				return 'QuestionCuePoint';
			if ($enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_ANSWER))
				return 'AnswerCuePoint';
		}
		if ($baseClass == 'UserEntry' && $enumValue == self::getCoreValue('UserEntryType' , QuizUserEntryType::QUIZ))
		{
			return QuizUserEntry::QUIZ_OM_CLASS;
		}

	}

	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::QUIZ_MANAGER,
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
					<xs:element name="quizUserEntryId" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
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
			if ( !is_null($object->getFromCustomData(self::QUIZ_DATA)) )
			{
				return array(self::getDynamicAttributeName() => 1);
			}
		}

		return array();
	}

	public static function getDynamicAttributeName()
	{
		return self::getPluginName() . '_' . self::IS_QUIZ;
	}

	/**
	 * @param entry $entry
	 * @return kQuiz
	 */
	public static function getQuizData( entry $entry )
	{
		$quizData = $entry->getFromCustomData( self::QUIZ_DATA );
		return $quizData;
	}

	/**
	 * @param entry $entry
	 * @param kQuiz $kQuiz
	 */
	public static function setQuizData( entry $entry, kQuiz $kQuiz )
	{
		$entry->putInCustomData( self::QUIZ_DATA, $kQuiz);
		$entry->addCapability(self::getCapatabilityCoreValue());
	}

	/**
	 * @param entry $dbEntry
	 * @return mixed|string
	 * @throws Exception
	 */
	public static function validateAndGetQuiz( entry $dbEntry ) {
		$kQuiz = self::getQuizData($dbEntry);
		if ( !$kQuiz )
			throw new kCoreException("Entry is not a quiz",kCoreException::INVALID_ENTRY_ID, $dbEntry->getId());

		return $kQuiz;
	}


	/**
	 * @param entry $dbEntry
	 * @return bool if current user is admin / entyr owner / co-editor
	 */
	public static function validateUserEntitledForQuizEdit( entry $dbEntry )
	{
		if ( kCurrentContext::$is_admin_session || kCurrentContext::getCurrentKsKuserId() == $dbEntry->getKuserId())
			return true;

		$entitledKusers = explode(',', $dbEntry->getEntitledKusersEdit());
		if(in_array(kCurrentContext::getCurrentKsKuserId(), $entitledKusers))
		{
			return true;
		}

		return false;
	}

	/**
	 * Receives the data needed in order to generate the total report of said plugin
	 *
	 * @param string $partner_id
	 * @param KalturaReportType $report_type
	 * @param string $objectIds
	 * @return array|null
	 */
	public function getReportResult($partner_id, $report_type, $report_flavor, $objectIds, $orderBy = null)
	{
		if (!in_array(str_replace(self::getPluginName().".", "", $report_type), QuizReportType::getAdditionalValues()))
		{
			return null;
		}
		switch ($report_flavor)
		{
			case myReportsMgr::REPORT_FLAVOR_TOTAL:
				return $this->getTotalReport($objectIds);
			case myReportsMgr::REPORT_FLAVOR_TABLE:
				if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ))
				{
					return $this->getQuestionPercentageTableReport($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_USER_PERCENTAGE))
				{
					return $this->getUserPercentageTable($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_AGGREGATE_BY_QUESTION))
				{
					return $this->getQuizQuestionPercentageTableReport($objectIds, $orderBy);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_USER_AGGREGATE_BY_QUESTION))
				{
					return $this->getUserPrecentageByUserTable($objectIds, $orderBy);
				}
			case myReportsMgr::REPORT_FLAVOR_COUNT:
				if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ))
				{
					return $this->getReportCount($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_USER_PERCENTAGE) )
				{
					return $this->getUserPercentageCount($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_AGGREGATE_BY_QUESTION))
				{
					return $this->getQuestionCountByQusetionIds($objectIds);
				}
				else if ($report_type == (self::getPluginName() . "." . QuizReportType::QUIZ_USER_AGGREGATE_BY_QUESTION))
				{
					return $this->getAnswerCountByUserIds($objectIds);
				}
			default:
				return null;
		}
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
		$kQuiz = self::getQuizData($dbEntry);
		if ( !$kQuiz )
			return array(array('average' => null));
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $objectIds);
		$c->add(UserEntryPeer::TYPE, QuizPlugin::getCoreValue('UserEntryType', QuizUserEntryType::QUIZ));
		$c->add(UserEntryPeer::STATUS, QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$anonKuser = $this->getAnonymousKuser($dbEntry->getPartnerId());
		if (!is_null($anonKuser))
		{
			$c->add(UserEntryPeer::KUSER_ID, $anonKuser->getKuserId(), Criteria::NOT_IN);
		}

		$quizzes = UserEntryPeer::doSelect($c);
		$numOfQuizzesFound = count($quizzes);
		KalturaLog::debug("Found $numOfQuizzesFound quizzes that were submitted");
		if ($numOfQuizzesFound)
		{
			$sumOfScores = 0;
			foreach ($quizzes as $quiz)
			{
				/**
				 * @var QuizUserEntry $quiz
				 */
				$sumOfScores += $quiz->getScore();
			}
			$avg = $sumOfScores / $numOfQuizzesFound;
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
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_QUESTION));
		$questions = CuePointPeer::doSelect($c);
		return $this->getAggregateDataForQuestions($questions, $orderBy);
	}

	
	protected function getQuizQuestionPercentageTableReport($objectIds, $orderBy)
	{
		$questionIds = explode(",", $objectIds);
		$questionsCriteria = new Criteria();
		$questionsCriteria->add(CuePointPeer::ID, $questionIds, Criteria::IN);
		$questionsCriteria->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_QUESTION));
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
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz($dbEntry);
		$ans = array();
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_QUESTION));
		$anonKuser = $this->getAnonymousKuser($dbEntry->getPartnerId());
		if (!is_null($anonKuser))
		{
			$c->add(CuePointPeer::KUSER_ID, $anonKuser->getKuserId(), Criteria::NOT_IN);
		}

		$numOfquestions = CuePointPeer::doCount($c);
		$res = array();
		$res['count_all'] = $numOfquestions;
		return array($res);
	}

	protected function getUserPercentageCount($objectIds)
	{
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_ANSWER));
				
		$answers = CuePointPeer::doSelect($c);
		$kuserIds = array();
		foreach ($answers as $answer)
		{
			if (isset($kuserIds[$answer->getKuserId()]))
			{
				$kuserIds[$answer->getKuserId()]++;
			}
			else
			{
				$kuserIds[$answer->getKuserId()] = 0;
			}
		}
		$numOfAnswers = count($kuserIds);
		$res = array();
		$res['count_all'] = $numOfAnswers;
		return array($res);
	}
	
	protected function getQuestionCountByQusetionIds($objectIds)
	{
		$questionIds = explode(",", $objectIds);
		$c = new Criteria();
		$c->add(CuePointPeer::ID, $questionIds, Criteria::IN);
		$numOfquestions = CuePointPeer::doCount($c);
		$res = array();
		$res['count_all'] = $numOfquestions;
		return array($res);
	}
	
	protected function getAnswerCountByUserIds($objectIds)
	{
		$c = $this->createGetCuePointByUserIdsCriteria($objectIds);
		$numOfAnswers = 0;
		$answers = CuePointPeer::doSelect($c);
		$quizUserEntryIds = array();
		foreach ($answers as $answer)
		{
			$quizUserEntryIds[]= $answer->getQuizUserEntryId();
		}
		$quizUserEntries = $this->getUserEntries($quizUserEntryIds);
				
		foreach ($answers as $answer)
		{
			/**
			 * @var AnswerCuePoint $answer
			 */
			$quizUserEntryId = $answer->getQuizUserEntryId();
			if ( isset($quizUserEntries[$quizUserEntryId]) &&
				$quizUserEntries[$quizUserEntryId]->getStatus() == self::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED) )
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
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $objectIds);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_ANSWER));
		$anonKuser = $this->getAnonymousKuser($dbEntry->getPartnerId());
		if (!is_null($anonKuser))
		{
			$c->add(CuePointPeer::KUSER_ID, $anonKuser->getKuserId(), Criteria::NOT_IN);
		}
		$answers = CuePointPeer::doSelect($c);
		return $this->getAggregateDataForUsers($answers, $orderBy);
	}
	
	protected function getUserPrecentageByUserTable($objectIds, $orderBy)
	{
		$c = $this->createGetCuePointByUserIdsCriteria($objectIds);
		$answers = CuePointPeer::doSelect($c);
		return $this->getAggregateDataForUsers($answers, $orderBy);
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
			$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_ANSWER));
			$c->add(CuePointPeer::PARENT_ID, $question->getId());
			$anonKuser = $this->getAnonymousKuser($question->getPartnerId());
			if (!is_null($anonKuser))
			{
				$c->add(CuePointPeer::KUSER_ID, $anonKuser->getKuserId(), Criteria::NOT_IN);
			}
			$answers = CuePointPeer::doSelect($c);
			$numOfAnswers = 0;
			foreach ($answers as $answer)
			{
				$quizUserEntryIds[]= $answer->getQuizUserEntryId();
			}
			$quizUserEntries = $this->getUserEntries($quizUserEntryIds);
			
			foreach ($answers as $answer)
			{
				/**
				 * @var AnswerCuePoint $answer
				 */
				$quizUserEntryId = $answer->getQuizUserEntryId();
				if ( isset($quizUserEntries[$quizUserEntryId]) &&
					$quizUserEntries[$quizUserEntryId]->getStatus() == self::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED) )
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
	 * @param $answers
	 * @param $orderBy
	 * @return array
	 */
	protected function getAggregateDataForUsers($answers, $orderBy)
	{
		$ans = array();
		$usersCorrectAnswers = array();
		$usersWrongAnswers = array();

		foreach ($answers as $answer)
		{
			$quizUserEntryIds[]= $answer->getQuizUserEntryId();
		}
		$quizUserEntries = $this->getUserEntries($quizUserEntryIds);
		
		foreach ($answers as $answer)
		{
			/**
			 * @var AnswerCuePoint $answer
			 */
			$quizUserEntryId = $answer->getQuizUserEntryId();
			if ( isset($quizUserEntries[$quizUserEntryId]) &&
				$quizUserEntries[$quizUserEntryId]->getStatus() == self::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED) )
			{
				if ($answer->getIsCorrect())
				{
					if (isset($usersCorrectAnswers[$answer->getKuserId()]))
					{
						$usersCorrectAnswers[$answer->getKuserId()]++;
					} else
					{
						$usersCorrectAnswers[$answer->getKuserId()] = 1;
					}
				} else
				{
					if (isset($usersWrongAnswers[$answer->getKuserId()]))
					{
						$usersWrongAnswers[$answer->getKuserId()]++;
					} else
					{
						$usersWrongAnswers[$answer->getKuserId()] = 1;
					}
				}
			}
		}

		foreach (array_merge(array_keys($usersCorrectAnswers), array_keys($usersWrongAnswers)) as $kuserId)
		{
			$totalAnswers = 0;
			$totalCorrect = 0;
			if (isset($usersCorrectAnswers[$kuserId]))
			{
				$totalAnswers += $usersCorrectAnswers[$kuserId];
				$totalCorrect = $usersCorrectAnswers[$kuserId];
			}
			if (isset($usersWrongAnswers[$kuserId]))
			{
				$totalAnswers += $usersWrongAnswers[$kuserId];
			}
			$userId = "unknown-user";
			$dbKuser = kuserPeer::retrieveByPK($kuserId);
			if ($dbKuser)
			{
				$userId = $dbKuser->getPuserId();
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

	/**
	 * @param $objectIds
	 * @return Criteria
	 */
	protected function createGetCuePointByUserIdsCriteria($objectIds)
	{
		$c = new Criteria();
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_ANSWER));
		$userIds = explode(",", $objectIds);
		$kuserIds = array();
		foreach ($userIds as $userId)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
			if ($kuser)
			{
				$kuserIds[] = $kuser->getKuserId();
			}
		}
		$c->add(CuePointPeer::KUSER_ID, $kuserIds, Criteria::IN);
		return $c;
	}
	
	//TODO: When cuePoints will be indexed in the sphinx we won't need this anymore since we'll be able to query the shpinx for this info
/*	protected function isQuizUserEntrySubmitted($quizUserEntryId)
	{
		$ans = false;
		$quizUserEntry = UserEntryPeer::retrieveByPK($quizUserEntryId);
		if ($quizUserEntry)
		{
			if ($quizUserEntry->getStatus() == self::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
			{
				$ans = true;
			}
		}
		return $ans;
	}
*/
	/**
	 * @param $partnerID
	 * @return kuser
	 */
	protected function getAnonymousKuser($partnerID)
	{
		$anonKuser = kuserPeer::getKuserByPartnerAndUid($partnerID, 0, true);
		return $anonKuser;
	}

	/**
	 * @param $quizUserEntryIds
	 * @return array
	 */
	protected function getUserEntries($quizUserEntryIds)
	{
		$quizUserEntriesDB = UserEntryPeer::retrieveByPKs($quizUserEntryIds);
		$quizUserEntries = array();
		foreach ($quizUserEntriesDB as $quizUserEntry)
		{
			$quizUserEntries[$quizUserEntry->getId()] = $quizUserEntry;
		}
		return $quizUserEntries;
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
