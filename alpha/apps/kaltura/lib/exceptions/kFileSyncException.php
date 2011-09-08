<?php

class kFileSyncException extends kCoreException
{
	const FILE_DOES_NOT_EXIST_ON_CURRENT_DC = 1;
	const FILE_SYNC_PARTNER_ID_NOT_DEFINED = 2;
}