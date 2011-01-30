<?php
interface ShortLinkStatus extends BaseEnum
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}