<?php
class KalturaFlavorAssetStatus extends KalturaEnum
{
	const ERROR = -1;
	const QUEUED = 0;
	const CONVERTING = 1;
	const READY = 2;
	const DELETED = 3;
	const NOT_APPLICABLE = 4;
	const TEMP = 5;
}
