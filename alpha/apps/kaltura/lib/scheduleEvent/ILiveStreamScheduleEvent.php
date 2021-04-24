<?php

interface ILiveStreamScheduleEvent extends IScheduleEvent
{
	public function getSourceEntryId();
}