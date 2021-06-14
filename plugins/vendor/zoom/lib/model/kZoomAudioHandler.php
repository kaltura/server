<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomAudioHandler
{
    public static function handleAudioFiles(&$recordingFilesPerTimeSlot, $meetingFileUuid, $mp4, $m4a, $fileDeletionPolicy, $manualDelete, $zoomClient, $recordingFileTypeKey = null, $idKey = null)
    {
        $foundMP4 = false;
        $audioKeys = array();
        foreach ($recordingFilesPerTimeSlot as $key => $recordingFile)
        {
            $recordingFileType = $recordingFileTypeKey ? $recordingFile[$recordingFileTypeKey] : $recordingFile->recordingFileType;
            if ($recordingFileType === $mp4)
            {
                $foundMP4 = true;
            }
            if ($recordingFileType === $m4a)
            {
                $audioKeys[] = $key;
            }
        }
        if ($foundMP4)
        {
            foreach ($audioKeys as $audioKey)
            {
                $audioRecordingFile = $recordingFilesPerTimeSlot[$audioKey];
                KalturaLog::debug('Video and Audio files were found. audio file is ' . print_r($audioRecordingFile, true) . ' , unsetting Audio');
                unset($recordingFilesPerTimeSlot[$audioKey]);
                if ($fileDeletionPolicy != $manualDelete)
                {
                    KalturaLog::debug('Deleting Audio File From Zoom ');
                    $audioRecordingFileId = $idKey ? $audioRecordingFile[$idKey] : $audioRecordingFile->id;
                    $zoomClient->deleteRecordingFile($meetingFileUuid, $audioRecordingFileId);
                }
            }
        }
    }
}