<?php
interface DistributionAction extends BaseEnum
{
	const SUBMIT = 1;
	const UPDATE = 2;
	const DELETE = 3;
	const FETCH_REPORT = 4;
}