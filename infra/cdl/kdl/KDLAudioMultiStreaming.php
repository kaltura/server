<?php

/**************************************
 * class KDLStreamDescriptor
 */
class KDLStreamDescriptor {
	protected $mapping = null;
	public	$olayout = null;
	public	$lang = null;
	public	$label = null;
	public $channels = null;
	public function __construct($mapping=null, $olayout=null, $lang=null, $label=null){
		$this->setMapping($mapping);
		$this->olayout = $olayout;
		$this->lang = $lang;
		$this->label = $label;
	}
	public function set($obj){
		if(isset($obj->mapping))	{ $this->setMapping($obj->mapping); }
		if(isset($obj->olayout))	{ $this->olayout = $obj->olayout; }
		if(isset($obj->lang))		{ $this->lang = $obj->lang; }
		if(isset($obj->label))		{ $this->label = $obj->label; }
	}

	/**
	 * 
	 * @return Ambigous <number, unknown, number>|number
	 */
	public function getLayoutChannels()
	{
		if (isset($this->olayout) ) {
			return KDLAudioLayouts::getLayoutChannels($this->olayout);
		}
		return 0;
	}

	/**
	 * 
	 * @return number
	 */
	public function getChannelsNum()
	{
		if(isset($this->channels)){
			$cnt = 0;
			foreach ($this->channels as $chIds){
				$cnt+=count($chIds);
			}
			return $cnt;
		}
		else {
			return count($this->mapping);
		}
			
	}
	
	/**
	 * 
	 * @return NULL
	 */
	public function getMapping(){
		if(isset($this->mapping))
			return $this->mapping;
		else
			return null;
	}
	
	/**
	 * 
	 * @param unknown_type $mapping
	 */
	public function setMapping($mapping){
		$this->mapping = $mapping;
		if(!isset($mapping)){
			return;
		}
		$channels = array();
		foreach ($mapping as $mIdx=>$map){
			$s;$c;
			$map=trim($map);
			if($map[0]=="*"){
				$s="*";
				$chr;
				$n = sscanf($map,"%c.%d",$chr, $c);
			}
			else {
				$n = sscanf($map,"%d.%d",$s, $c);
			}
			if($n<2)
				continue;
			
			if(!array_key_exists($s,$channels)) {
				$channels[$s] = array($c);
				$this->mapping[$mIdx] = $s;
			}
			else {
				$channels[$s][] = $c;
				unset($this->mapping[$mIdx]);
			}
		}
		$this->channels = count($channels)>0? $channels: null;
	}
	
	/**
	 *
	 * @param unknown_type $sourceStreams
	 * @param unknown_type $sourceAnalize
	 * @return NULL|KDLStreamDescriptor
	 */
	public function generateTarget($sourceAudioStreams, $sourceAnalize)
	{
		/*
		 * Porcess mapped channels - both from flavor params and from override
		 */
		if(isset($this->channels) || isset($sourceAnalize->override->perTrackChannelIndexLabel) ){
			$target = $this->generateTargetMappedChannels($sourceAudioStreams, $sourceAnalize);
		}
		/*
		 * If no mapping - try to generate using lingual notation
		 * 	go to default/nonmapped flow
		 */
		else if((!isset($this->mapping) || count($this->mapping)==0)){
			return $this->generateTargetLingualNotated($sourceAudioStreams, $sourceAnalize);
		}
		else {
			$target = $this->generateTargetMappedStreams($sourceAudioStreams, $sourceAnalize);
		}
		
		if(isset($target)){
			// Turn on downmix, if any
			$target->adjustForDownmix($sourceAudioStreams);
		}
		return $target;
	}

	/**
	 *
	 * @param unknown_type $sourceStreams
	 * @return boolean
	 */
	private function adjustForDownmix($sourceAudioStreams)
	{
		if(!isset($this->mapping) || count($this->mapping)>1)
			return false;
		foreach($this->mapping as $m){
			foreach($sourceAudioStreams as $sourceStream){
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
	private function filterSourceToMapping(array $sourceStreams, $verifyFields=false, array &$filteredStreams)
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
	
	/**
	 *
	 * Multichannel source streams -
	 * 	Check for multichannel source streams that match the required layout (by number of channels)
	 * 	If there are such source streams, choose one that inlcuded in the this::mapping list,
	 * 	if there is no this::mapping - use the first multichannel source stream (that matches ...) 
	 * 
	 * otherwise - 
	 * 1 ch source streams
	 * 	If this::olayout represents 'supported layout', filter in the source streams for that layout
	 * 		Check compliance with the predefined mapping
	 * 
	 * @param unknown_type $sourceStreams
	 * @param unknown_type $sourceAnalize
	 * @return NULL|KDLStreamDescriptor
	 */
	private function generateTargetMappedStreams($sourceStreams, $sourceAnalize)
	{
		/*
		 * Determine how many channels are required for the requested olayout
		 */
		$olayoutNum = $this->getLayoutChannels();
		$target = clone $this;
	
		/*
		 * Check for multichannel source streams that match the required layout ... 
		 */
		if(isset($this->olayout) 
		&& isset($sourceAnalize->perChannelNumber) && array_key_exists($olayoutNum, $sourceAnalize->perChannelNumber)){
			if(!isset($this->mapping)){
				$sourceStream = $sourceAnalize->perChannelNumber[$olayoutNum][0];
				$target->mapping = array($sourceStream->id);
				if(isset($sourceStream->audioLabel)) $target->label = $sourceStream->audioLabel;
				return $target;
			}
			foreach($sourceAnalize->perChannelNumber[$olayoutNum] as $sourceStream){
				if(in_array($sourceStream->id, $this->mapping)){
					$target->mapping = array($sourceStream->id);
					if(isset($sourceStream->audioLabel)) $target->label = $sourceStream->audioLabel;
					return $target;
				}
			}
		}
	
		/*
		 * The rest of the flow requires ????
		 */
		
		/*
		 * Check whether the 'olayout' value is in the list of the 'supported layouts'
		 * If it is - get the layout and filter the mapped streams that are in the layout
		 */
		if(key_exists((string)$this->olayout, KDLAudioLayouts::$layouts)) {
			$layout = KDLAudioLayouts::$layouts[$this->olayout];
			$layoutStreams = KDLAudioLayouts::matchLayouts($sourceStreams, $layout);
			/*
			 * There are channel notations in the source file -
			 * and the layout matching succeeded - use only the matched streams as source streams
			 * for the following phases
			 */
			if(count($layoutStreams)>0){
				$sourceStreams = $layoutStreams;
			}
		}
	
		/*
		 * Filter-in the source streams to match the predifined mapping
		 */
		$sourceToMappingStreams = array();
		if(isset($this->olayout)) {
			$sourceStreamCnt = $target->filterSourceToMapping($sourceStreams, true, $sourceToMappingStreams);
		}
		else {
			$sourceStreamCnt = $target->filterSourceToMapping($sourceStreams, false, $sourceToMappingStreams);
			/*
			 * The this::olayout is not set -
			 * ==> leave with mapping only.
			 */
			if($sourceStreamCnt>0) {
				$target->olayout = null;
				if(isset($sourceToMappingStreams[0]->audioLabel)) $target->label = $sourceToMappingStreams[0]->audioLabel;
				return $target;
			}
		}
			/*
			 * If there is no source streams that match the predifiend mapping - 
			 * ==> multi-stream won't work, leave w/out multistream setting
			 */
		if($sourceStreamCnt==0)
			return null;
		
		/*
		 * Verify matching between matched source streams and target::::olayout
		 */
	
		/*
		 * If there are not enough matchedStreams to match the olayout
		* or the number of mapping ids is less than required for the olayout
		* - fallback to default (stereo)
		*/
		if($olayoutNum>$target->getChannelsNum()){
			if($sourceStreamCnt==1)
				return null;
			
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
	
		if(isset($sourceToMappingStreams[0]->audioLabel)) $target->label = $sourceToMappingStreams[0]->audioLabel;
		return $target;
	}
	
	/**
	 * Filter in the source streams that are included in the this::mapping(streams/channels) definition
	 * Try to match between the filtered sources and this::olayout
	 * The 'channels' option support only a single(first) input stream!!!
	 * 
	 * On 'override' (sourceAnalize::override)
	 * 	If language is set (this::lang) - check availability of the required language in the overriden objects
	 * 	otherwise - leave with null (fullback to default flow)
	 * 	Non 'override' flow does not requires source-to-this::channels language matching, because it assumed 
	 * 	that regular source streams cannot have per channel language notation
	 *  
	 * Channels availability - 
	 * 	Check availability (in source) of all mapped channels (this::channels), filter out unavailable streams/channels
	 * 	If no channels are left, the default flow (non mapped) will be applied
	 * 
	 * Layout -
	 * 	Check whether there are enough channels to support the required olayout,
	 * 	if not - fallback to stereo (but still use the mapped channels)
	 * 
	 * @param unknown_type $sourceStreams
	 * @param unknown_type $sourceAnalize
	 * @return NULL|KDLStreamDescriptor
	 */
	private function generateTargetMappedChannels($sourceStreams, $sourceAnalize)
	{
		/*
		 * Adjust stream settings to 'wildcarded' channel mapping ("*.1,*.2, ..."), by choosing the first 
		 * source stream that contains enough channels to support the channel mapping
		 */
		if($this->mapping[0]=='*'){
			$maxChanId = 0;
			foreach ($this->channels['*'] as $channelId){
				if($maxChanId<$channelId) $maxChanId=$channelId;
			}
			ksort($sourceAnalize->perChannelNumber);
			foreach($sourceAnalize->perChannelNumber as $sorceChannelsNum=>$subArr){
				if($sorceChannelsNum>=$maxChanId) {
					$this->mapping[0]=$sourceAnalize->perChannelNumber[$sorceChannelsNum][0]->id;
					$this->channels[$this->mapping[0]] = $this->channels['*'];
					unset($this->channels['*']);
					break;
				}
			}
			if($sorceChannelsNum<$maxChanId){
				;
			}
		}
		
		$thisLayoutChannels = $this->getLayoutChannels();
		/*
		 * If override channel mapped data is provided, 
		 * use it instead of 'plain' sourcestream settings
		 */
		if(isset($sourceAnalize->override->perTrackChannelIndexLabel)){
			/*
			 * On output stream language flow, look for mapping for required language (this::lang),
			 * otherwise leave with null (fallback to default flow)
			 */
			if(isset($this->lang)) {
				/*
				 * If specific language is required, but it does not exist in the override settings - 
				 * 	return null (fallback to default)
				 */
				if(!(isset($sourceAnalize->override->perLanguage) && key_exists($this->lang, $sourceAnalize->override->perLanguage)) ){
					return null;
				}

				/*
				 * Verify that the selected override::source is conatined in the current stream mapping (this::mapping)
				 */
				foreach ($sourceAnalize->override->perTrackChannelIndexLanguage as $streaamIdx=>$perTrackChannelIndexLanguage) {
					if(isset($this->mapping) && array_search($streaamIdx, $this->mapping)===false){
						continue;			
					}
					if(key_exists($this->lang, $perTrackChannelIndexLanguage)) {
						$target = $this->generateTargetChannels($perTrackChannelIndexLanguage[$this->lang]);
						if(isset($target)){
							break;
						}
					}
				}
			}
			/*
			 * Non language flow - 
			 * Scan through the defined (or auto) labels to look for the stream that best matches the 
			 * olayout settings (this::olayout) by checking the number of target channeles
			 */
			else {
				$auxTarget = null;
				foreach ($sourceAnalize->override->perTrackChannelIndexLabel as $trackIdx=>$perLabel){
					if(isset($this->mapping) && array_search($trackIdx, $this->mapping)===false){
						continue;			
					}
					foreach ($perLabel as $labelName=>$overrideChannels){
						$target = $this->generateTargetChannels($overrideChannels);
						if(isset($target)){
							/*
							 * If sufficent number of channels (for this::olayout) -
							 * skip following optional mappings
							 */
							if($target->getChannelsNum()>=$thisLayoutChannels)
								break;
							if(!isset($auxTarget) || $auxTarget->getChannelsNum()<$target->getChannelsNum()){
								$auxTarget = $target;
								$target = null;
							}
						}
					}
					if(isset($target)){
						break;
					}
				}
			}
		}
		else {
			/*
			 * Channel processing is limited to a single audio stream (track) - 
			 * look for the stream that best matches the olayout settings (this::olayout) 
			 * by checking the number of target channeles
			 */
			$auxTarget = null;
			foreach($this->channels as $streamIdx=>$streamChannels){
				foreach($sourceStreams as $sourceStream){
					if($streamIdx!=$sourceStream->id)
						continue;

					$matchedChannelsArr = array();
					foreach ($streamChannels as $channelId){
						if(isset($sourceStream->audioChannels) && $sourceStream->audioChannels>$channelId){
							$matchedChannelsArr[] = $channelId;
						}
					}
					if(count($matchedChannelsArr)>0){
						$target = new KDLStreamDescriptor();
						$target->channels[$streamIdx] = $matchedChannelsArr;
						$target->mapping[] = $streamIdx;
						if($target->getChannelsNum()>=$thisLayoutChannels)
							break;
						if(!isset($auxTarget) || $auxTarget->getChannelsNum()<$target->getChannelsNum()){
							$auxTarget = $target;
							$target = null;
						}
					}
				}
				if(isset($target)){
					break;
				}
			}
		}
		
		/*
		 * Verify that there is a valid target,
		 * if not leave with null (fallback to default flow)
		 * 
		 */
		{
			if(!isset($target)){
				if(isset($auxTarget)){
					$target = $auxTarget;
				}
				else {
					return null;
				}
			}
			$targetChannelsNum = $target->getChannelsNum();
			if($targetChannelsNum==0){
				return null;
			}
		}
		
		/*
		 * Determine how many channels are required for the requested olayout
		 */
		if(isset($this->olayout) && $targetChannelsNum>=$this->getLayoutChannels()){
			$target->olayout = $this->olayout;
		}
		else {
			$target->olayout=$targetChannelsNum>=2? 2: 0;
		}

		$target->lang = isset($this->lang)? $this->lang: null;
		
		return $target;
	}

	/**
	 * 
	 * @param unknown_type $overrideChannels
	 * @return NULL
	 */
	private function generateTargetChannels($overrideChannels){
		$matchedChannelsArr = array();
		foreach($overrideChannels as $overrideItem){
			if(!isset($this->channels)
			|| (key_exists($overrideItem->id, $this->channels) && isset($overrideItem->audioChannelIndex) && array_search($overrideItem->audioChannelIndex, $this->channels[$overrideItem->id])!==false)){
				$matchedChannelsArr[] = $overrideItem->audioChannelIndex;
			}
		}
		if(count($matchedChannelsArr)==0) {
			return null;
		}
		
		$target = new KDLStreamDescriptor();
		
		if(isset($overrideItem->audioChannelIndex)){
			$target->channels[$overrideItem->id] = $matchedChannelsArr;
		}
		$target->mapping[] = $overrideItem->id;
		if(isset($overrideItem->audioLabel)) $target->label = $overrideItem->audioLabel;
/**/		
		$targetChannelsNum = $target->getChannelsNum();
		if($targetChannelsNum==0){
			return null;
		}
		
		$olayoutNum = $this->getLayoutChannels();
	
		if($targetChannelsNum>=$olayoutNum){
			$target->olayout = $this->olayout;
		}
		else {
			$target->olayout=$targetChannelsNum>=2? 2: 0;
		}

		return $target;
	}
	
	/**
	 * 
	 * @param unknown_type $sourceStreams
	 * @param unknown_type $sourceAnalize
	 */
	private function generateTargetLingualNotated($sourceStreams, $sourceAnalize)
	{
		if(!isset($this->lang)){
			return null;
		}
		
		if(!isset($sourceAnalize->languages) || !key_exists($this->lang, $sourceAnalize->languages)){
			return null;
		}

		$streams = $sourceAnalize->languages[$this->lang];
		/*
		 * Currently handle just the first language entity
		 */
		if(isset($streams[0]->audioChannels)){
			$olayout = (isset($this->olayout) && $this->olayout>0)? $this->olayout: $streams[0]->audioChannels;
			$target = new KDLStreamDescriptor(array($streams[0]->id),$olayout,$this->lang);
		}
		else {
			$target =  new KDLStreamDescriptor(array($streams[0]->id),0,$this->lang);
		}
		if(isset($streams[0]->audioLabel)) {
			$target->label = $streams[0]->audioLabel;
		}
		return $target;
	}
}

/***************************************
 * class KDLAudioMultiStreaming
 */
class KDLAudioMultiStreaming {
	public $streams = array();
	public $action;
	
	public function __construct($settings=null, $sourceOverride=null)
	{
		$this->LoadSettings($settings, $sourceOverride);
	}

	/**
	 *
	 * @param unknown_type $settings
	 */
	public function LoadSettings($settings=null)
	{
		if(!isset($settings))
			return;

		if(isset($settings->action)) $this->action = $settings->action;
		
		$toLoad = array();
		/*
		 * 'New' multiStream format
		 */
		if(isset($settings->streams)){
			foreach ($settings->streams as $obj){
				$this->addStream($obj);		
			}
			return;
		}

		/*
		 * 'Old' mu;ltistream format,
		 * 	convert into new format 
		 */
		if(is_array($settings)) {
			$toLoad = $settings;
		}
		else  {
			$toLoad = array($settings);
		}
		foreach ($toLoad as $obj){
			if(!isset($obj->languages))
				continue;

			foreach ($obj->languages as $lang){
				if(is_object($lang)){
					$this->addStream($lang);
				}
				else {
					$this->addStream(null, $lang);
				}
			}
		}
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
			$num = max($num, $stream->getChannelsNum());
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
		return $this->streams[$oStreamIdx]->getMapping();
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
	
	/**
	 * 
	 * @param unknown_type $multiStreamObj
	 * @param unknown_type $field
	 * @param unknown_type $idx
	 * @return boolean
	 */
	public static function IsStreamFieldSet($multiStreamObj, $field, $idx=0)
	{
		if(isset($multiStreamObj->audio->streams)
				&& count($multiStreamObj->audio->streams)>0
				&& isset($multiStreamObj->audio->streams[$idx]->$field)){
			return true;
		}
		else {
			return false;
		}
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
	public function GetSettings($sourceStreams, $overrideStreams=null)
	{
		$sourceAudioStreams = isset($sourceStreams->audio)? $sourceStreams->audio: null;
		$overrideStreams = isset($overrideStreams->audio)? $overrideStreams->audio: null;
		
		$overrideAudio = self::prepareSourceOverride($overrideStreams, $sourceAudioStreams);
		if(isset($overrideAudio)){
			self::applyOverrideToSource($overrideAudio->perTrack, $sourceAudioStreams);
		}
		$sourceAnalize = self::analizeSourceContentStreams($sourceStreams);
		$sourceAnalize->override = $overrideAudio;
		
		/*
		 * The 'default' flow (multiStream object is not provided) -
		 * Check analyze results for
		 * - 'streamsAsChannels' - process them as sorround streams
		 * - otherwise remove the 'multiStream' object'
		 */
		{
			if(!$this->isInitialzed()){
				$targetMultiAudio =  self::overrideChannelsToTarget($sourceAudioStreams, $sourceAnalize);
				if(isset($targetMultiAudio))
					return $targetMultiAudio;

				$targetMultiAudio =  self::surroundAudioSourceToTarget($sourceAudioStreams, $sourceAnalize);
				if(isset($targetMultiAudio))
					return $targetMultiAudio;

				/*
				 * If there are at least two mono streams - auto-setup 2 channel multiStream in order to support stereo output
				 */
				if(isset($sourceAnalize->perChannelNumber) && key_exists(1, $sourceAnalize->perChannelNumber) && count($sourceAnalize->perChannelNumber[1])>=2){
					$targetMultiAudio = self::multipleMonoSourceStreamsToTarget($sourceAudioStreams, $sourceAnalize->perChannelNumber[1]);
				}
				return $targetMultiAudio;
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
		$streams = $this->initializeSetupStreams($sourceAudioStreams);

		/*
		 *
		 */
		$targetMultiAudio = new KDLAudioMultiStreamingHelper();
		foreach ($streams as $idx=>$stream){
			$target = $stream->generateTarget($sourceAudioStreams, $sourceAnalize);
			if(isset($target)){
				$targetMultiAudio->streams[] = $target;
			}
		}
		if(isset($this->action)) $targetMultiAudio->action = $this->action;
		if(count($targetMultiAudio->streams)==0)
			return null;

		return $targetMultiAudio;
	}

	/**
	 *
	 * @param unknown_type $sourceStreams
	 */
	private function initializeSetupStreams($sourceAudioStreams)
	{
		/*
		 * If 'all' isset, then filter in all source streams -
		* - for 'separate' as standalone streams
		* - otherwise - as mapping in the first stream and remove the rest
		*/
		$streams = array();
		$firstStream = $this->streams[0];
		$firstStreamMapping = $firstStream->getMapping();
		if(isset($firstStreamMapping) && count($firstStreamMapping)>0 && (string)$firstStreamMapping[0]=='all'){
			if($this->action=='separate'){
				foreach($sourceAudioStreams as $sourceStream){
					$stream = new KDLStreamDescriptor(array($sourceStream->id), $firstStream->olayout, $firstStream->lang);
					$streams[] = $stream;
				}
			}
			else {
				$streamIds = array();
				foreach($sourceAudioStreams as $sourceStream){
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
				// Identical concidered to be less than 500msec delta
				if($dlt<500){
					$identicalDur[$t][] = $stream;
				}
				else {
					$differentDur[$t][] = $stream;
				}

			}
		}

		/*
		 * For audio streams -
		 * Check for 'streamAsChannel' and 'multilangual' stream sets.
		 * If the streams duration is too diverse (>2) - skip (probably should be concated)
		 * 'streamAsChannel' considered to be if there are more than 1 mono streams.
		 */
		if(array_key_exists('audio', $identicalDur) && count($identicalDur['audio'])>1
		&& (count($contentStreams->audio)-count($identicalDur['audio']))<=2){
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
		}

		/*
		 * Sort audio streams by the number of channels
		 */
		$perChannelNumber = array();
		if(isset($contentStreams->audio)){
			// Get all streams that have 'surround' like audio layout - FR, FL, ...
			// Sort the audio streams for channel number. We are looking for mono streams
			foreach ($contentStreams->audio as $stream){
				if(isset($stream->audioChannels))
					$perChannelNumber[$stream->audioChannels][] = $stream;
			}
		}

		$rvAnalize = new stdClass();
			// Set 'streamsAsChannels' only if there are more than 1 audio streams in the file
		if(isset($channelStreams) && count($channelStreams)>1){
			$rvAnalize->streamsAsChannels = $channelStreams;
		}
			// Set 'languages' only if there are more than 1 language in the file
		if(isset($langStreams) && count($langStreams)>1){
			$rvAnalize->languages = $langStreams;
		}
		if(count($identicalDur)>0) $rvAnalize->identicalDur = $identicalDur;
		if(count($differentDur)>0) $rvAnalize->differentDur = $differentDur;
		if(count($zeroedDur)>0) $rvAnalize->zeroedDur = $zeroedDur;
		if(count($perChannelNumber)>0) $rvAnalize->perChannelNumber = $perChannelNumber;

		return $rvAnalize;
	}

	/**
	 * Override records should be gathered by -
	 * 	track id
	 * 	language
	 * 	label
	 * @param unknown_type $sourceOverride
	 */
	protected static function prepareSourceOverride($overrides, $sourceAudioStreams)
	{
		if(!isset($overrides))
			return null;
		
		$auxSources = array();
		foreach ($sourceAudioStreams as $sourceAudioStream){
			$auxSources[$sourceAudioStream->id] = $sourceAudioStream; 
		}
			
		/*
		 * Gather together all sourceOverride's that belong to the same track/stream (id)/label/channels.
		 */
		$perTrack = array();
		$perLanguage = array();
		$perLabel = array();
		$perTrackChannelIndexLanguage = array();
		$perTrackChannelIndexLabel = array();
		foreach ($overrides as $idx=>$override){
/**/
			/*
			 * Filter out overrides that do not match source tracks(streams)/channels
			 */
			if(!key_exists($override->id, $auxSources)) {
				continue;
			}
			$auxSource = $auxSources[$override->id];
			if(isset($override->audioChannelIndex) && isset($auxSource->audioChannels) 
			&& $override->audioChannelIndex>=$auxSource->audioChannels) {
				continue;
			}

			$perTrack[$override->id][] = $override;
			if(isset($override->audioLanguage)){
				$perLanguage[$override->audioLanguage][] = $override;
			}
			if(isset($override->audioLabel)){
				$perLabel[$override->audioLabel][] = $override;
			}
			
			if(isset($override->audioChannelIndex)){
					if(isset($override->audioLabel)){
						$perTrackChannelIndexLabel[$override->id][$override->audioLabel][] = $override;
					}
					else {
						$perTrackChannelIndexLabel[$override->id]["und"][] = $override;
					}
					if(isset($override->audioLanguage)){
						$perTrackChannelIndexLanguage[$override->id][$override->audioLanguage][] = $override;
					}
					else {
//						$perTrackChannelIndexLanguage[$override->id]["und"][] = $override;
					}
			}
		}
		
		
		$rv = new stdClass();
		$rv->perTrack = 	count($perTrack)>0? $perTrack: null;
		$rv->perLanguage = 	count($perLanguage)>0? $perLanguage: null;
		$rv->perLabel = 	count($perLabel)>0? $perLabel: null;
		$rv->perTrackChannelIndexLabel = 	count($perTrackChannelIndexLabel)>0? $perTrackChannelIndexLabel: null;
		$rv->perTrackChannelIndexLanguage = count($perTrackChannelIndexLanguage)>0? $perTrackChannelIndexLanguage: null;
	
		return $rv;
	}

	/**
	 * Override original source values
	 * 	Only the first override record (per track) is applied and only if there is no channel settings there, 
	 * 	otherwise assume it is channel mapping and skip 
	 * @param unknown_type $overridesPerTrack
	 * @param unknown_type $sourceAudioStreams
	 */
	protected static function applyOverrideToSource($overridesPerTrack, $sourceAudioStreams)
	{
		if(!isset($overridesPerTrack))
			return;
		foreach($sourceAudioStreams as $idx=>$sourceAudioStream){
			if(key_exists($sourceAudioStream->id, $overridesPerTrack) && !isset($overridesPerTrack[$sourceAudioStream->id][0]->audioChannelIndex)){
				$override = $overridesPerTrack[$sourceAudioStream->id][0];
				$fields = get_object_vars($override);
				foreach ($fields as $field=>$val){
					if(isset($override->$field)){
						$sourceAudioStream->$field=$val;
					}
				}
				$sourceAudioStreams[$idx] = $sourceAudioStream;
			}
		}
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
	public static function surroundAudioSourceToTarget($sourceAudioStreams, $sourceAnalize)
	{	
		if(!isset($sourceAnalize->streamsAsChannels))
			return null;
		if(!isset($sourceAudioStreams))
			return null;
		
		$analyzedStreams = $sourceAnalize->streamsAsChannels;
			/*
			 * Try to find most suitible source streams to resample into stereo output -
			 * - downmix
			 * - FL,FR,mono(center) 
			 * - DL,DR
			 * If found the source can be porcessed into stereo
			 */
		$mappedStreams = KDLAudioLayouts::matchLayouts($sourceAudioStreams, KDLAudioLayouts::DOWNMIX);
		if(count($mappedStreams)==0) {
			$mappedStreams = KDLAudioLayouts::matchLayouts($analyzedStreams, array(KDLAudioLayouts::FL, KDLAudioLayouts::FR, KDLAudioLayouts::MONO,));
			if(count($mappedStreams)==0) {
				$mappedStreams = KDLAudioLayouts::matchLayouts($analyzedStreams, array(KDLAudioLayouts::DL, KDLAudioLayouts::DR));
			}
		}
		
		$mapping = array();
		foreach ($mappedStreams as $mappedStream){
			$mapping[] = $mappedStream->id;
		}
		$stream = new KDLStreamDescriptor();
		$stream->setMapping(count($mapping)>0? $mapping: null);
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
	 * Setup automatic multistream out of several un notated mono audio streams.
	 * Mix the fist 2 mono streams into a stereo
	 * 
	 * @param unknown_type $source
	 * @param unknown_type $analyzedStreams
	 */
	public static function multipleMonoSourceStreamsToTarget($sourceAudioStreams, $analyzedStreams)
	{
		if(!isset($sourceAudioStreams))
			return null;

		$mapping = array();
		$mapping[] = $analyzedStreams[0]->id;
		$mapping[] = $analyzedStreams[1]->id;
		$stream = new KDLStreamDescriptor();
		$stream->olayout = 2;
		$stream->setMapping($mapping);
		
		$target = new KDLAudioMultiStreaming();
		$target->streams[] = $stream;
		return $target;
	}

	/**
	 * 
	 * @param unknown_type $sourceAudioStreams
	 * @param unknown_type $sourceAnalize
	 */
	public static function overrideChannelsToTarget($sourceAudioStreams, $sourceAnalize)
	{
		$layoutMajor = array(KDLAudioLayouts::FL, KDLAudioLayouts::FR, KDLAudioLayouts::MONO);
		$layoutMinor = array(KDLAudioLayouts::DL, KDLAudioLayouts::DR);

		/*
		 * Look for labled channels, try to adjust one of the 'downmix'able layout (majir/minor)
		 * If not found - look for channels with language notation
		 */
		$mappedChannels = array();
		if(isset($sourceAnalize->override->perTrackChannelIndexLabel)) {
			$mappedChannels = self::searchThroughOverride($sourceAnalize->override->perTrackChannelIndexLabel, $layoutMajor);
			if(count($mappedChannels)<count($layoutMajor))
				$mappedChannels = self::searchThroughOverride($sourceAnalize->override->perTrackChannelIndexLabel, $layoutMinor);
		}
		if(isset($sourceAnalize->override->perTrackChannelIndexLanguage)) {
			if(count($mappedChannels)<count($layoutMinor))
				$mappedChannels = self::searchThroughOverride($sourceAnalize->override->perTrackChannelIndexLanguage, $layoutMajor);
			if(count($mappedChannels)<count($layoutMajor))
				$mappedChannels = self::searchThroughOverride($sourceAnalize->override->perTrackChannelIndexLanguage, $layoutMinor);
		}
		/*
		 * If 'labled' and 'languaged' records not found,
		 * try to match non notated channels.
		 * Peference order - 2channels per track, more than 2channels, one channel
		 
		if(count($mappedChannels)==0){
			$perChannelCnt = array();
			if(isset($sourceAnalize->override->perTrack))
				foreach($sourceAnalize->override->perTrack as $trackIdx=>$track){
					$perChannelCnt[count($track)][] = $track;
				}
			if(key_exists(2, $perChannelCnt)){
				$mappedChannels = $perChannelCnt[2][0];
			}
			else {
				if(key_exists(1, $perChannelCnt)){
					$mappedOneChannel = $perChannelCnt[1][0];
				}
				foreach($perChannelCnt as $cnt=>$aux){
					if($cnt>2){
						$mappedManyChannels = $aux[0];
						break;
					}
				}
				if(isset($mappedManyChannels))
					$mappedChannels = array_slice($mappedManyChannels, 0, 2);
				else if(isset($mappedOneChannel))
					$mappedChannels = $mappedOneChannel;
			}
		}
		*/
		/*
		 * If no mapped channels - get out
		 */
		if(count($mappedChannels)==0){
			return null;
		}

		/*
		 * 
		 */
		$stream = new KDLStreamDescriptor();
		$mapping = array();
		foreach ($mappedChannels as $mappedChannel){
			$mapping[] = $mappedChannels[0]->id.".".$mappedChannel->audioChannelIndex;
		}
		if(count($mapping)>1){
			$stream->olayout = 2;
		}
		$stream->setMapping($mapping);
		
		$target = new KDLAudioMultiStreaming();
		$target->streams[] = $stream;
		return $target;
	}
	
	/**
	 * 
	 * @param unknown_type $override
	 * @param unknown_type $layouts
	 * @return Ambigous <multitype:, multitype:unknown, multitype:unknown >|multitype:
	 */
	public static function searchThroughOverride($override, $layouts)
	{
		$mappedStreams = array();
		if(isset($override)) {
			foreach ($override as $trkIdx=>$perTrack){
				foreach($perTrack as $langIdx=>$perLang){
					$mappedStreams = KDLAudioLayouts::matchLayouts($perLang, $layouts);
					if(count($mappedStreams)==count($layouts)) {
						return $mappedStreams;
					}
					$mappedStreams = array();
				}
			}
		}
		return $mappedStreams;
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
