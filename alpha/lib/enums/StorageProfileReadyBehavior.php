<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface StorageProfileReadyBehavior extends BaseEnum
{
    const NO_EFFECT = 0; // does not affect the exported asset status
    const REQUIRED = 1; // exported asset will be ready only after export finished
}