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

class Kaltura_Client_Type_FlavorParams extends Kaltura_Client_Type_AssetParams
{
	public function getKalturaObjectType()
	{
		return 'KalturaFlavorParams';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->videoCodec = (string)$xml->videoCodec;
		if(count($xml->videoBitrate))
			$this->videoBitrate = (int)$xml->videoBitrate;
		$this->audioCodec = (string)$xml->audioCodec;
		if(count($xml->audioBitrate))
			$this->audioBitrate = (int)$xml->audioBitrate;
		if(count($xml->audioChannels))
			$this->audioChannels = (int)$xml->audioChannels;
		if(count($xml->audioSampleRate))
			$this->audioSampleRate = (int)$xml->audioSampleRate;
		if(count($xml->width))
			$this->width = (int)$xml->width;
		if(count($xml->height))
			$this->height = (int)$xml->height;
		if(count($xml->frameRate))
			$this->frameRate = (int)$xml->frameRate;
		if(count($xml->gopSize))
			$this->gopSize = (int)$xml->gopSize;
		$this->conversionEngines = (string)$xml->conversionEngines;
		$this->conversionEnginesExtraParams = (string)$xml->conversionEnginesExtraParams;
		if(!empty($xml->twoPass))
			$this->twoPass = true;
		if(count($xml->deinterlice))
			$this->deinterlice = (int)$xml->deinterlice;
		if(count($xml->rotate))
			$this->rotate = (int)$xml->rotate;
		$this->operators = (string)$xml->operators;
		if(count($xml->engineVersion))
			$this->engineVersion = (int)$xml->engineVersion;
		$this->format = (string)$xml->format;
		if(count($xml->aspectRatioProcessingMode))
			$this->aspectRatioProcessingMode = (int)$xml->aspectRatioProcessingMode;
		if(count($xml->forceFrameToMultiplication16))
			$this->forceFrameToMultiplication16 = (int)$xml->forceFrameToMultiplication16;
		if(count($xml->isGopInSec))
			$this->isGopInSec = (int)$xml->isGopInSec;
		if(count($xml->isAvoidVideoShrinkFramesizeToSource))
			$this->isAvoidVideoShrinkFramesizeToSource = (int)$xml->isAvoidVideoShrinkFramesizeToSource;
		if(count($xml->isAvoidVideoShrinkBitrateToSource))
			$this->isAvoidVideoShrinkBitrateToSource = (int)$xml->isAvoidVideoShrinkBitrateToSource;
		if(count($xml->isVideoFrameRateForLowBrAppleHls))
			$this->isVideoFrameRateForLowBrAppleHls = (int)$xml->isVideoFrameRateForLowBrAppleHls;
		if(count($xml->videoConstantBitrate))
			$this->videoConstantBitrate = (int)$xml->videoConstantBitrate;
		if(count($xml->videoBitrateTolerance))
			$this->videoBitrateTolerance = (int)$xml->videoBitrateTolerance;
		if(count($xml->clipOffset))
			$this->clipOffset = (int)$xml->clipOffset;
		if(count($xml->clipDuration))
			$this->clipDuration = (int)$xml->clipDuration;
	}
	/**
	 * The video codec of the Flavor Params
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_VideoCodec
	 */
	public $videoCodec = null;

	/**
	 * The video bitrate (in KBits) of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $videoBitrate = null;

	/**
	 * The audio codec of the Flavor Params
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_AudioCodec
	 */
	public $audioCodec = null;

	/**
	 * The audio bitrate (in KBits) of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $audioBitrate = null;

	/**
	 * The number of audio channels for "downmixing"
	 * 	 
	 *
	 * @var int
	 */
	public $audioChannels = null;

	/**
	 * The audio sample rate of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $audioSampleRate = null;

	/**
	 * The desired width of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * The desired height of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * The frame rate of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $frameRate = null;

	/**
	 * The gop size of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $gopSize = null;

	/**
	 * The list of conversion engines (comma separated)
	 * 	 
	 *
	 * @var string
	 */
	public $conversionEngines = null;

	/**
	 * The list of conversion engines extra params (separated with "|")
	 * 	 
	 *
	 * @var string
	 */
	public $conversionEnginesExtraParams = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $twoPass = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deinterlice = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rotate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $operators = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $engineVersion = null;

	/**
	 * The container format of the Flavor Params
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_ContainerFormat
	 */
	public $format = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $aspectRatioProcessingMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $forceFrameToMultiplication16 = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isGopInSec = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isAvoidVideoShrinkFramesizeToSource = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isAvoidVideoShrinkBitrateToSource = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isVideoFrameRateForLowBrAppleHls = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoConstantBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoBitrateTolerance = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipDuration = null;


}

