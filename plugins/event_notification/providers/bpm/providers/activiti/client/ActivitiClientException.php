<?php

class ActivitiClientException extends Exception
{
	const CLASS_NOT_FOUND = 1;
	const PAGE_INVALID = 2;
	const PAGE_SIZE_INVALID = 3;
	const INVALID_HTTP_CODE = 4;
	const NO_VALID_RESPONSE = 5;
}
