<?php

class KFFMpegMediaParserAdStitchHelper extends KFFMpegMediaParser
{
    public function __construct($rawData)
    {
        $this->filePath = $rawData;
    }

    /**
     * @method
     * @return string
     */
    public function getRawMediaInfo($filePath=null)
    {
        return $this->filePath;
    }

    /**
     * @method
     * @param unknown_type $mediaInfo
     * @return KDLMediaDataSet
     */
    static public function mediaInfoToKDL($mediaInfo)
    {
        $medSet = new KDLMediaDataSet();

        $medSet->_container = new KDLContainerData();

        $contentStreams = $mediaInfo->contentStreams;
        if(isset($contentStreams)) {
            $fromJson = json_decode($contentStreams);
            $medSet->_contentStreams = isset($fromJson)? $fromJson: null;
        }


        $medSet->_container->_id=$mediaInfo->containerId;
        $medSet->_container->_format=$mediaInfo->containerFormat;
        $medSet->_container->_duration=$mediaInfo->containerDuration;
        $medSet->_container->_bitRate=$mediaInfo->containerBitRate;
        $medSet->_container->_fileSize=$mediaInfo->fileSize;
        if($medSet->_container->IsDataSet()==false)
            $medSet->_container = null;

        $medSet->_video = new KDLVideoData();
        $medSet->_video->_id = $mediaInfo->videoCodecId;
        $medSet->_video->_format = $mediaInfo->videoFormat;
        $medSet->_video->_duration = $mediaInfo->videoDuration;
        $medSet->_video->_bitRate = $mediaInfo->videoBitRate;
        $medSet->_video->_width = $mediaInfo->videoWidth;
        $medSet->_video->_height = $mediaInfo->videoHeight;
        $medSet->_video->_frameRate = $mediaInfo->videoFrameRate;
        $medSet->_video->_dar = $mediaInfo->videoDar;
        $medSet->_video->_rotation = $mediaInfo->videoRotation;
        $medSet->_video->_scanType = $mediaInfo->scanType;
        if($medSet->_video->IsDataSet()==false)
            $medSet->_video = null;

        $medSet->_audio = new KDLAudioData();
        $medSet->_audio->_id = $mediaInfo->audioCodecId;
        $medSet->_audio->_format = $mediaInfo->audioFormat;
        $medSet->_audio->_duration = $mediaInfo->audioDuration;
        $medSet->_audio->_bitRate = $mediaInfo->audioBitRate;
        $medSet->_audio->_channels = $mediaInfo->audioChannels;
        $medSet->_audio->_sampleRate = $mediaInfo->audioSamplingRate;
        $medSet->_audio->_resolution = $mediaInfo->audioResolution;
        if($medSet->_audio->IsDataSet()==false)
            $medSet->_audio = null;
        return $medSet;
    }
}
