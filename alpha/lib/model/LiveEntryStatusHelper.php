<?php

class LiveEntryStatusHelper
{
    public static function maxLiveEntryStatusStatus($primaryMediaServerStatus, $secondaryMediaServerStatus)
    {
        if ($primaryMediaServerStatus == LiveEntryStatus::PLAYABLE || $secondaryMediaServerStatus == LiveEntryStatus::PLAYABLE)
            return LiveEntryStatus::PLAYABLE;
        elseif ($primaryMediaServerStatus == LiveEntryStatus::BROADCASTING || $secondaryMediaServerStatus == LiveEntryStatus::BROADCASTING)
            return LiveEntryStatus::BROADCASTING;
        else
            return LiveEntryStatus::STOPPED;
    }
}