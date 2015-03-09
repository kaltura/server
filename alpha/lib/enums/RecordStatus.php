<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface RecordStatus extends BaseEnum
{
   const DISABLED = 0;
   const APPENDED = 1;
   const PER_SESSION = 2;
}