<?php
interface EmailNotificationRecipientProviderType extends BaseEnum
{
	const STATIC_LIST = 1;
	
	const CATEGORY = 2;
	
	const USER = 3;
	
	const GROUP = 4;
}