<?php

/*
 * Encoding.com API: http://www.encoding.com/wdocs/ApiDoc
 * 
 * @package Scheduler
 * @subpackage Conversion
 */

class KEncodingComData
{
	const ACTION_ADD_MEDIA = 'AddMedia';
	const ACTION_GET_STATUS = 'GetStatus';
	const CBR_1PASS = 0;
	const VBR_1PASS = 1;
	const CBR_2PASS = 2;
	const VBR_2PASS = 3;
	
	/**
	 * @var array
	 */
	private $data = array(
		'query' => array(
		    'userid' => null,
		    'userkey' => null,
		    'action' => null,
		    'mediaid' => null,
		    'source' => null,
		    'notify' => null,
		
		    'format' => array(
		        'output' => null,
		        'video_codec' => null,
		        'audio_codec' => null,
		        'bitrate' => null,
		        'framerate' => null,
		        'audio_bitrate' => null,
		        'audio_sample_rate' => null,
		        'audio_volume' => null,      
		        'size' => null,
		        'two_pass' => null,
		        'cbr' => null,
		        'crop_left' => null,
		        'crop_top' => null,
		        'crop_right' => null,
		        'crop_bottom' => null,
		        'thumb_time' => null,
		        'thumb_size' => null,
		        'add_meta' => null,
		       
		        'rc_init_occupancy' => null,
				'deinterlacing' => null,
		        'minrate' => null,
		        'maxrate' => null,
		        'bufsize' => null,
		
		        'keyframe' => null,
		        'start' => null,
		        'duration' => null,
		
		        'destination' => null,
		        'thumb_destination' => null,
	
		        'turbo' => null,
			)
		)
	);
	
	/**
	 * @param array $arr
	 * @return string
	 */
	private function getArrayXml(array $arr)
	{
		$ret = null;
		
		foreach($arr as $key => $value)
		{
			$valueStr = $value;
			if(is_array($value))
				$valueStr = $this->getArrayXml($value);
				
			if(is_null($valueStr))
				continue;
				
			$ret .= "<$key>$valueStr</$key>";
		}
		return $ret;
	}
	
	/**
	 * @return string
	 */
	public function getXml()
	{
		return "<?xml version=\"1.0\"?>" . $this->getArrayXml($this->data);
	}
	
	public function setUserId($v)
	{
		$this->data['query']['userid'] = $v;
	}
	
	public function setUserKey($v)
	{
		$this->data['query']['userkey'] = $v;
	}
	
	public function setAction($v)
	{
		$this->data['query']['action'] = $v;
	}
	
	public function setMediaId($v)
	{
		$this->data['query']['mediaid'] = $v;
	}
	
	public function setSource($v)
	{
		$this->data['query']['source'] = $v;
	}
	
	public function setNotify($v)
	{
		$this->data['query']['notify'] = $v;
	}
	
	
	public function setFormatOutput($v)
	{
		$this->data['query']['format']['output'] = $v;
	}
	
    public function setFormatVideoCodec($v)
	{
		$this->data['query']['format']['video_codec'] = $v;
	}
	
    public function setFormatAudioCodec($v)
	{
		$this->data['query']['format']['audio_codec'] = $v;
	}
	
    public function setFormatBitrate($v)
	{
		$this->data['query']['format']['bitrate'] = $v;
	}
	
    public function setFormatFramerate($v)
	{
		$this->data['query']['format']['framerate'] = $v;
	}
	
    public function setFormatAudioBitrate($v)
	{
		if(is_numeric($v))
			$v .= 'k';
			
		$this->data['query']['format']['audio_bitrate'] = $v;
	}
	
    public function setFormatAudioSampleRate($v)
	{
		$this->data['query']['format']['audio_sample_rate'] = $v;
	}
	
    public function setFormatAudioVolume($v)
	{
		$this->data['query']['format']['audio_volume'] = $v;
	}
	      
    public function setFormatSize($v)
	{
		$this->data['query']['format']['size'] = $v;
	}
	
    public function setFormatTwoPass($v)
	{
		$this->data['query']['format']['two_pass'] = $v;
	}
	
    public function setFormatCbr($v)
	{
		$this->data['query']['format']['cbr'] = $v;
	}
	
    public function setFormatCropLeft($v)
	{
		$this->data['query']['format']['crop_left'] = $v;
	}
	
    public function setFormatCropTop($v)
	{
		$this->data['query']['format']['crop_top'] = $v;
	}
	
    public function setFormatCropRight($v)
	{
		$this->data['query']['format']['crop_right'] = $v;
	}
	
    public function setFormatCropBottom($v)
	{
		$this->data['query']['format']['crop_bottom'] = $v;
	}
	
    public function setFormatThumbTime($v)
	{
		$this->data['query']['format']['thumb_time'] = $v;
	}
	
    public function setFormatThumbSize($v)
	{
		$this->data['query']['format']['thumb_size'] = $v;
	}
	
    public function setFormatAddMeta($v)
	{
		$this->data['query']['format']['add_meta'] = $v;
	}
	
       
    public function setFormatRcInitOccupancy($v)
	{
		$this->data['query']['format']['rc_init_occupancy'] = $v;
	}
	
    public function setFormatDeinterlacing($v)
	{
		$this->data['query']['format']['deinterlacing'] = $v;
	}
	
    public function setFormatMinRate($v)
	{
		$this->data['query']['format']['minrate'] = $v;
	}
	
    public function setFormatMaxRate($v)
	{
		$this->data['query']['format']['maxrate'] = $v;
	}
	
    public function setFormatBufSize($v)
	{
		$this->data['query']['format']['bufsize'] = $v;
	}
	

    public function setFormatKeyFrame($v)
	{
		$this->data['query']['format']['keyframe'] = $v;
	}
	
    public function setFormatStart($v)
	{
		$this->data['query']['format']['start'] = $v;
	}
	
    public function setFormatDuration($v)
	{
		$this->data['query']['format']['duration'] = $v;
	}
	

    public function setFormatDestination($v)
	{
		$this->data['query']['format']['destination'] = $v;
	}
	
    public function setFormatThumbDestination($v)
	{
		$this->data['query']['format']['thumb_destination'] = $v;
	}
	
               
    public function setFormatTurbo($v)
	{
		$this->data['query']['format']['turbo'] = $v;
	}
	
}
