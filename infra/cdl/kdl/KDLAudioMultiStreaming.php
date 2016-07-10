<?php

/**************************************
 * class KDLStreamDescriptor
 */
class KDLStreamDescriptor {
	public 	$mapping = null;
	public	$olayout = null;
	public	$lang = null;
	public function __construct($mapping=null, $olayout=null, $lang=null){
		$this->mapping = $mapping;
		$this->olayout = $olayout;
		$this->lang = $lang;
	}
	public function set($obj){
		if(isset($obj->mapping))	{ $this->mapping = $obj->mapping; }
		if(isset($obj->olayout))	{ $this->olayout = $obj->olayout; }
		if(isset($obj->lang))		{ $this->lang = $obj->lang; }
	}

	public function getLayoutChannels()
	{
		if (isset($this->olayout) ) {
			return KDLAudioLayouts::getLayoutChannels($this->olayout);
		}
		return 0;
	}

	/**
	 *
	 * @param unknown_type $sourceStreams
	 * @param unknown_type $sourceAnalize
	 * @return NULL|KDLStreamDescriptor
	 */
	public function GetSettings($sourceStreams, $sourceAnalize)
	{
		if(!isset($this->olayout) && (!isset($this->mapping) || count($this->mapping)==0)){
			return null;
		}

		/*
		 * Determine how many channels are required for the requested olayout
		 */
		$olayoutNum = $this->getLayoutChannels();
		$target = clone $this;

		if(isset($this->olayout) && isset($sourceAnalize->byChannelNumber)
				&& array_key_exists($olayoutNum, $sourceAnalize->byChannelNumber)){
			$sourceStream = $sourceAnalize->byChannelNumber[$olayoutNum][0];
			if(!isset($this->mapping) || in_array($sourceStream->id, $this->mapping)){
				$target->mapping = array($sourceStream->id);
				return $target;
			}
		}

		/*
		 * Check whether the 'olayout' value is in the list of the 'supported layouts'
		 * If it is - get the layout and filter the mapped streams that are in the layout
		 */
		if(key_exists((string)$this->olayout, KDLAudioLayouts::$layouts)) {
			$layout = KDLAudioLayouts::$layouts[$this->olayout];
			$matchedStreams = KDLAudioLayouts::matchLayouts($sourceStreams, $layout);
			/*
			 * There are channel notations in the source file -
			* and the matching succeeded - use the matched streames instead of mapped streams
			* even so try to use the non notated mapped streams
			*/
			if(count($matchedStreams)>0){
				$sourceStreams = $matchedStreams;
			}
		}

		/*
		 * Filter-in the required streams -
		 * either from the 'setupStreams' array or 'all'
		 */
		$mappingStreams = array();
		if(!isset($this->olayout)) {
			$target->filterToMapping($sourceStreams, false, $mappingStreams);
			$target->olayout = null;
			return $target;
		}
		$target->filterToMapping($sourceStreams, true, $mappingStreams);

		/*
		 * Verify matching between matchedStreams and setupMultiStream::olayout
		 */

		/*
		 * If there are not enough matchedStreams to match the olayout
		 * or the number of mapping ids is less than required for the olayout
		 * - fallback to default (stereo)
		 */
		if($olayoutNum>count($mappingStreams) || $olayoutNum>count($target->mapping)){
			$target->olayout = null;
		}
		else {
			/*
			 * Too many mappedStreams - cut to the number of 'olayout' streams
			 
			if($olayoutNum<count($sourceStreams)){
				$sourceStreams = array_slice($sourceStreams, 0, $olayoutNum);
			}*/
			$target->olayout = $this->olayout;
		}

		$target->mapping = array();
		foreach ($mappingStreams as $stream){
			$target->mapping[] = $stream->id;
		}

		return $target;
	}

	/**
	 *
	 * @param unknown_type $sourceStreams
	 * @return boolean
	 */
	public function adjustForDownmix($sourceStreams)
	{
		if(!isset($this->mapping) || count($this->mapping)>1)
			return false;
		foreach($this->mapping as $m){
			foreach($sourceStreams->audio as $sourceStream){
				if($sourceStream->id==$m
						&& isset($sourceStream->audioChannelLayout)	&& $sourceStream->audioChannelLayout==KDLAudioLayouts::DOWNMIX){
					$this->downmix = 1;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *
	 * @param KDLStreamDescriptor $stream
	 * @param array $sourceStreams
	 * @param unknown_type $verifyFields
	 * @param array $filteredStreams
	 * @return number
	 */
	private function filterToMapping(array $sourceStreams, $verifyFields=false, array &$filteredStreams)
	{
		$streamsIds = array();
		foreach($sourceStreams as $sourceStream){
			if(isset($this->mapping) && array_search($sourceStream->id, $this->mapping)===false && $this->mapping[0]!="all"){
				continue;
			}
			if(count($filteredStreams)==0 || $verifyFields==false || KDLAudioMultiStreamingHelper::isSimilarSourceStreams($filteredStreams[0], $sourceStream)){
				$filteredStreams[] = $sourceStream;
				$streamsIds[] = $sourceStream->id;
			}
		}
		$this->mapping = $streamsIds;
		return count($filteredStreams);
	}

}

/***************************************
 * class KDLAudioMultiStreaming
 */
class KDLAudioMultiStreaming {
	public $streams = array();
	public $action;

	public function __construct($settings=null)
	{
		$this->LoadSettings($settings);
	}

	/**
	 *
	 * @param unknown_type $settings
	 */
	public function LoadSettings($settings=null)
	{
		if(!isset($settings))
			return;

		$toLoad = array();
		if(is_array($settings)) {
			$toLoad = $settings;
		}
		else if(isset($settings->streams)){
			$toLoad = $settings->streams;
		}
		else {
			$toLoad = array($settings);
		}

		foreach ($toLoad as $obj){
			if(isset($obj->languages)){
				foreach ($obj->languages as $lang){
					if(is_object($lang)){
						$this->addStream($lang);
					}
					else {
						$this->addStream(null, $lang);
					}
				}
				continue;
			}
			else
				$this->addStream($obj);
		}

		if(isset($settings->action)) $this->action = $settings->action;
	}

	/**
	 *
	 * @return number
	 */
	public function getAudioChannels()
	{
		$multiStreamChannels = $this->getLayoutChannels();
		if($multiStreamChannels==0 && (!isset($this->action) || $this->action!="separate")){
			$multiStreamChannels = $this->getChannelsNum();
		}
		return (int)$multiStreamChannels;
	}

	/**
	 *
	 * @return Ambigous <number, mixed>
	 */
	private function getChannelsNum()
	{
		$num = 0;
		foreach ($this->streams as $stream) {
			$num = max($num, count($stream->mapping));
		}
		return $num;
	}

	/**
	 *
	 */
	public function getLayoutChannels()
	{
		$num = 0;
		foreach ($this->streams as $stream) {
			$num = max($num,$stream->getLayoutChannels());
		}
		return $num;
	}

	/**
	 *
	 * @return NULL|multitype:NULL
	 */
	protected function getLanguages()
	{
		$languages = array();
		foreach ($this->streams as $stream){
			if(isset($stream->lang))
				$languages[] = $stream->lang;
		}
		if(count($languages)==0)
			return null;
		return $languages;
	}

	/**
	 *
	 * @param unknown_type $oStreamIdx
	 * @return NULL
	 */
	public function getStreamMapping($oStreamIdx=0)
	{
		if(count($this->streams)<=$oStreamIdx)
			return null;
		return $this->streams[$oStreamIdx]->mapping;
	}

	/**
	 *
	 * @param unknown_type $oStreamIdx
	 * @return NULL
	 */
	public function getStreamLayout($oStreamIdx=0)
	{
		if(count($this->streams)<=$oStreamIdx)
			return null;
		return $this->streams[$oStreamIdx]->olayout;
	}

	/**
	 *
	 * @param unknown_type $obj
	 * @param unknown_type $lang
	 */
	public function addStream($obj, $lang=null){
		$stream = new KDLStreamDescriptor();
		if(isset($obj)){
			$stream->set($obj);
		}
		else{
			$stream->lang = $lang;
		}
		$this->streams[] = $stream;
	}
}

/***************************************************
 * class KDLAudioMultiStreamingHelper
 */
class KDLAudioMultiStreamingHelper extends KDLAudioMultiStreaming {

	/**
	 *
	 * @param unknown_type $sourceStreams
	 * @return Ambigous <-, NULL, KDLAudioMultiStreaming>|Ambigous <NULL, KDLAudioMultiStreaming>|NULL|KDLAudioMultiStreamingHelper
	 */
	public function GetSettings($sourceStreams)
	{

		$sourceAnalize = self::analizeSourceContentStreams($sourceStreams);

		/*
		 * The 'default' flow -
		* Check analyze results for
		* - 'streamsAsChannels' - process them as sorround streams
		* - 'languages - process them as multi-lingual
		* - otherwise remove the 'multiStream' object'
		*/
		{
			$setupLanguages = $this->getLanguages();
			if(isset($setupLanguages) && isset($sourceAnalize->languages)){
				return $this->multiLingualAudioSurceToTarget($sourceAnalize->languages);
			}
			else if(!$this->isInitialzed()){
				if(isset($sourceAnalize->streamsAsChannels)){
					return self::surroundAudioSurceToTarget($sourceStreams, $sourceAnalize->streamsAsChannels);
				}
				return null;
			}
		}

		if(!isset($this->streams) || count($this->streams)==0){
			return null;
		}
			
		/*
		 * If 'all' isset, then filter in all source streams -
		* - for 'separate' as standalone streams
		* - otherwise - as mapping in the first stream and remove the rest
		*/
		$streams = $this->initializeStreams($sourceStreams);

		/*
		 *
		*/
		$targetMultiAudio = new KDLAudioMultiStreamingHelper();
		foreach ($streams as $idx=>$stream){
			$stream = $stream->GetSettings($sourceStreams->audio, $sourceAnalize);
			if(isset($stream)){
				// Turn on downmix, if any
				$stream->adjustForDownmix($sourceStreams);
				$targetMultiAudio->streams[] = $stream;
			}
		}
		if(isset($this->action)) $targetMultiAudio->action = $this->action;

		return $targetMultiAudio;
	}

	/**
	 *
	 * @param unknown_type $sourceStreams
	 */
	private function initializeStreams($sourceStreams)
	{
		/*
		 * If 'all' isset, then filter in all source streams -
		* - for 'separate' as standalone streams
		* - otherwise - as mapping in the first stream and remove the rest
		*/
		$streams = array();
		$firstStream = $this->streams[0];
		if(isset($firstStream->mapping) && count($firstStream->mapping)>0 && $firstStream->mapping[0]=='all'){
			if($this->action=='separate'){
				foreach($sourceStreams->audio as $sourceStream){
					$stream = new KDLStreamDescriptor(array($sourceStream->id), $firstStream->olayout, $firstStream->lang);
					$streams[] = $stream;
				}
			}
			else {
				$streamIds = array();
				foreach($sourceStreams->audio as $sourceStream){
					$streamIds[] = $sourceStream->id;
				}
				$stream = new KDLStreamDescriptor($streamIds, $firstStream->olayout, $firstStream->lang);
				$streams = array($stream);
			}
		}
		else {
			$streams = $this->streams;
		}
		return $streams;
	}

	/**
	 *
	 * @param unknown_type $contentStreams
	 */
	private static function analizeSourceContentStreams($contentStreams)
	{
		$rvAnalize = new stdClass();

		/*
		 * Evaluate stream duration differences
		* - calc average duration of each stream type (vid, aud,...)
		* - calc delta between the avg and every stream dur (per type - aud, vid,...)
		* - evaluate which streams have dur identical to the avg, which one is diff, and which one is zeroed
		* - store in array for final part of anaylize logic
		*/
		$dursAccm = array();
		foreach($contentStreams as $t=>$streams) {
			foreach($streams as $stream){
				if(!array_key_exists($t, $dursAccm))
					$dursAccm[$t] = 0;
				$fld = $t."Duration";
				$dursAccm[$t] += isset($stream->$fld)?$stream->$fld:0;
			}
		}
		foreach($dursAccm as $t=>$accm){
			$dursAvg[$t] = $accm>0?$accm/count($contentStreams->$t):0;
		}

		$identicalDur = array();
		$zeroedDur = array();
		$differentDur = array();
		foreach($contentStreams as $t=>$streams) {
			foreach($streams as $stream){
				$fld = $t."Duration";
				if(!isset($stream->$fld)){
					$dur=0;
				}
				else
					$dur = $stream->$fld; //audoDuration or videoDuration or dataDuration
				if($dur==0) {
					$zeroedDur[$t][] = $stream;
					continue;
				}

				$dlt = abs($dursAvg[$t]-$dur);
				// Identical concidered to be less than 1sec delta and the delts is less than 0.1%
				if($dlt<1000 && $dlt/$dur<0.001)
					$identicalDur[$t][] = $stream;
				else
					$differentDur[$t][] = $stream;

			}
		}

		/*
		 * For audio streams -
		* Check for 'streamAsChannel' case and for 'multilangual' case
		* 'streamAsChannel' considerd to be if there are more than 1 mono streams.
		*/
		if(array_key_exists('audio', $identicalDur) && count($identicalDur['audio'])>1){
			// Get all streams that have 'surround' like audio layout - FR, FL, ...
			$channelStreams = KDLAudioLayouts::matchLayouts($identicalDur['audio']);

			// Sort the audio streams for channel number. We are looking for mono streams
			$chnNumStreams = array();
			foreach ($channelStreams as $stream){
				if(isset($stream->audioChannels))
					$chnNumStreams[$stream->audioChannels][] = $stream;
			}

			/*
				* The streams that might be used for merging are only mono streams
			* otherwise - no streamAsChannel
			*/
			if(array_key_exists(1, $chnNumStreams) && count($chnNumStreams[1])>1){
				$channelStreams = $chnNumStreams[1];
			}
			else {
				$channelStreams = array();
			}
				
			/*
				* Check for multi-langual case
			* Sort the streams according to stream language
			*/
			$langStreams = array();
			foreach ($identicalDur['audio'] as $stream){
				if(isset($stream->audioLanguage))
					$langStreams[$stream->audioLanguage][] = $stream;
			}
				
			// Set 'streamsAsChannels' only if there are more than 2 audio streams in the file
			if(count($channelStreams)>1){
				$rvAnalize->streamsAsChannels = $channelStreams;
			}
			// Set 'languages' only if there are more than 1 language in the file
			if(count($langStreams)>1){
				$rvAnalize->languages = $langStreams;
			}	// not overlayed streams, probably should be concated
			if(count($contentStreams->audio)-count($identicalDur['audio'])>2){
				$rvAnalize = null;
			}

		}

		////////////////////////
		if(isset($contentStreams->audio)){
			// Get all streams that have 'surround' like audio layout - FR, FL, ...
			// Sort the audio streams for channel number. We are looking for mono streams
			$byChannelNumber = array();
			foreach ($contentStreams->audio as $stream){
				if(isset($stream->audioChannels))
					$byChannelNumber[$stream->audioChannels][] = $stream;
			}
		}

		////////////////////////
		if(count($identicalDur)>0) $rvAnalize->identicalDur = $identicalDur;
		if(count($differentDur)>0) $rvAnalize->differentDur = $differentDur;
		if(count($zeroedDur)>0) $rvAnalize->zeroedDur = $zeroedDur;
		if(count($byChannelNumber)>0) $rvAnalize->byChannelNumber = $byChannelNumber;

		return $rvAnalize;
	}

	/**
	 *
	 */
	public function isInitialzed()
	{
		return (isset($this->action) || (isset($this->streams) && count($this->streams)>0));
	}

	/**
	 *
	 * @param unknown_type $source
	 * @param unknown_type $analyzedStreams
	 */
	public static function surroundAudioSurceToTarget($sourceStreams, $analyzedStreams)
	{
		if(!isset($sourceStreams->audio))
			return null;


		$mappedStreams = KDLAudioLayouts::matchLayouts($sourceStreams->audio, KDLAudioLayouts::DOWNMIX);
		if(count($mappedStreams)==0) {
			$mappedStreams = KDLAudioLayouts::matchLayouts($analyzedStreams, array(KDLAudioLayouts::FL, KDLAudioLayouts::FR, KDLAudioLayouts::MONO,));
		}
		$stream = new KDLStreamDescriptor();
		foreach ($mappedStreams as $mappedStream){
			$stream->mapping[] = $mappedStream->id;
		}
		if(count($mappedStreams)==1){
			if(isset($mappedStream->audioChannels)){
				$stream->olayout = $mappedStream->audioChannels;
			}
			if(isset($mappedStream->audioChannelLayout)	&& $mappedStream->audioChannelLayout==KDLAudioLayouts::DOWNMIX){
				$stream->downmix = 1;
			}
		}
		if(count($mappedStreams)>1){
			$stream->olayout = 2;
		}
		$target = new KDLAudioMultiStreaming();
		$target->streams[] = $stream;
		return $target;
	}

	/**
	 *
	 * @param unknown_type $source
	 * @param unknown_type $languages
	 * @return -
	 * 	null - not applicable for that source (non multi-lingual) or for the required multiStream settings (no multi-lingual requirement)
	 * 	stdClass obj with set audio-languages array - holding matched languages
	 * 	stdClass obj with empty audio-languages array - no matched languages
	 */
	public function multiLingualAudioSurceToTarget($sourceLanguages)
	{
		/*
		 * If no multi-lingual data in the source, get out
		*/
		if(!(is_array($sourceLanguages) && count($sourceLanguages)>0)){
			return null;
		}

		/*
		 * Sample json string:
		* 		- {"audio":{"languages":["eng","esp"]}}
		*/
		$target = new KDLAudioMultiStreaming();
		foreach ($sourceLanguages as $lang=>$streamsPerLanguage){
			if(count($streamsPerLanguage)>1) {
				return null;
			}
			if(($idx=$this->lookForLanguage($lang))===false){
				continue;
			}
				
			/*
			 * Currently handle just the first language entity
			*/
			if(isset($streamsPerLanguage[0]->audioChannels))
				$stream = new KDLStreamDescriptor(array($streamsPerLanguage[0]->id),$streamsPerLanguage[0]->audioChannels,$lang);
			else
				$stream = new KDLStreamDescriptor(array($streamsPerLanguage[0]->id),null,$lang);
			$target->streams[] = $stream;
		}

		if(count($target->streams)==0)
			return null;

		return($target);
	}

	/**
	 *
	 * @param unknown_type $stream1
	 * @param unknown_type $stream2
	 * @return boolean
	 */
	public static function isSimilarSourceStreams($stream1, $stream2)
	{
		if(((!isset($stream1->audioFormat) 	   && !isset($stream2->audioFormat))     || $stream1->audioFormat    ==$stream2->audioFormat)
				&& ((!isset($stream1->audioDuration)   && !isset($stream2->audioDuration))   || $stream1->audioDuration  ==$stream2->audioDuration)
				&& ((!isset($stream1->audioChannels)   && !isset($stream2->audioChannels))   || $stream1->audioChannels  ==$stream2->audioChannels)
				&& ((!isset($stream1->audioSampleRate) && !isset($stream2->audioSampleRate)) || $stream1->audioSampleRate==$stream2->audioSampleRate)){
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param unknown_type $langName
	 * @return unknown|boolean
	 */
	private function lookForLanguage($langName)
	{
		foreach ($this->streams as $idx=>$stream){
			if(isset($stream->lang) && $stream->lang==$langName){
				return $idx;
			}
		}
		return false;
	}

}


?>
