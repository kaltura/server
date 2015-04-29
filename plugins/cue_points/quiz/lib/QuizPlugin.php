<?php
/**
 * Enable question cue point objects and answer cue point objects management on entry objects
 * @package plugins.quiz
 */
class QuizPlugin extends KalturaPlugin implements IKalturaCuePoint, IKalturaServices, IKalturaDynamicAttributeContributer
{
	const PLUGIN_NAME = 'quiz';

	const CUE_POINT_VERSION_MAJOR = 1;
	const CUE_POINT_VERSION_MINOR = 0;
	const CUE_POINT_VERSION_BUILD = 0;
	const CUE_POINT_NAME = 'cuePoint';


	const ANSWERS_OPTIONS = "answersOptions";

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
		);
		return $map;
	}


	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if ( is_null($baseEnumName) || ($baseEnumName == 'CuePointType') )
			return array('QuizCuePointType');

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
			if ( $enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUESTION))
				return new KalturaQuestionCuePoint();

			if ( $enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::ANSWER))
				return new KalturaAnswerCuePoint();
		}

	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint') {
			if ($enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::QUESTION))
				return 'QuestionCuePoint';
			if ($enumValue == self::getCuePointTypeCoreValue(QuizCuePointType::ANSWER))
				return 'AnswerCuePoint';
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		//TODO
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
						<xs:element name="title" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
						<xs:element name="description" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
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

		';
		return $xsd;
	}

	/* (non-PHPdoc)
 	* @see IKalturaCuePoint::getCuePointTypeCoreValue()
 	*/
	public static function getCuePointTypeCoreValue($valueName)
	{
		//TODO not sure
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('CuePointType', $value);
	}

	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getApiValue()
	 */
	public static function getApiValue($valueName)
	{	//TODO not sure
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getTypesToIndexOnEntry()
	{
		return array();
		///TODO not sure
		//return array(self::getCuePointTypeCoreValue(QuestionAnswerCuePointType::QUESTION),self::getCuePointTypeCoreValue(QuestionAnswerCuePointType::ANSWER));
	}

	/* (non-PHPdoc)
	 * @see IKalturaDynamicAttributeContributer::getDynamicAttribute()
	 */
	public static function getDynamicAttribute(entry $entry)
	{
		$isQuiz = 0;
		if ( !is_null($entry->getFromCustomData(self::QUIZ_DATA)) )
			$isQuiz = 1;

		$dynamicAttribute = array(self::getDynamicAttributeName() => $isQuiz);
		return $dynamicAttribute;
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

		if($quizData)
			$quizData = unserialize($quizData);

		return $quizData;
	}

	/**
	 * @param entry $entry
	 * @param kQuiz $kQuiz
	 */
	public static function setQuizData( entry $entry, kQuiz $kQuiz )
	{
		$entry->putInCustomData( self::QUIZ_DATA, serialize($kQuiz) );
	}
}