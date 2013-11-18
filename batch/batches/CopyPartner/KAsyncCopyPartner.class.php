<?php
/**
 * Copy an entire partner to and new one
 *
 * @package Scheduler
 * @subpackage CopyPartner
 */
class KAsyncCopyPartner extends KJobHandlerWorker
{
	protected $fromPartnerId;
	protected $toPartnerId;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY_PARTNER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$retVal = $this;

		try
		{
			$retVal = $this->doCopyPartner($job, $job->data);
		}
		catch ( Exception $e )
		{
			$this->log( $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString() );
		}
		
		self::unimpersonate(); // Make sure we're not impresonating anymore
		
		$this->log("KAsyncCopyPartner done.");
		
		return $retVal;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function doCopyPartner(KalturaBatchJob $job, KalturaCopyPartnerJobData $jobData)
	{
		$this->fromPartnerId = $jobData->fromPartnerId;
		$this->toPartnerId = $jobData->toPartnerId;
		$this->log( "KAsyncCopyPartner::doCopyPartner(), Job id [$job->id]" );
		
		$this->log( "CopyPartner job id [$job->id]" );

		// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
 		$this->copyPermissions();

 		$res = $this->closeJob($job, null, null, "CopyPartner finished", KalturaBatchJobStatus::FINISHED);
		return $res;
	}
	
	/**
	 * copyPermissions()
	 */
	protected function copyPermissions()
	{
		$this->log( "Copying permissions" );
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 0;
		
		$totalReceivedObjectsCount = 0;
		
		/* @var $this->getClient() KalturaClient */
		do
		{			
			self::impersonate( $this->fromPartnerId );
			$permissionsList = $this->getClient()->permission->listAction( null, $pageFilter );
			
			$totalCount = $permissionsList->totalCount;
			$receivedObjectsCount = count($permissionsList->objects);
			$totalReceivedObjectsCount += $receivedObjectsCount; 
			$pageFilter->pageIndex++;
			
			$this->log( "Got $receivedObjectsCount permission object(s) [= $totalReceivedObjectsCount/$totalCount]" );
			
			if ( $receivedObjectsCount > 0 )
			{
				self::impersonate( $this->toPartnerId );
				
				foreach ( $permissionsList->objects as $permission )
				{
					/* @var $permission KalturaPermission */
					if ( $permission->partnerId != 0 )
					{
						$newPermission = CopyPropertiesHelper::copyPublicWritableProperties( $permission, new KalturaPermission() );
						
						try
						{
							$this->getClient()->permission->add( $newPermission );
							$added = true;
						}
						catch ( Exception $e )
						{
							// We assume the exception was thrown because the premission already exists
							$added = false;
						}
						
						if ( $added )
						{
							$this->log( "Added permission: " . $permission->name );
						}							
					}
				}
			}			
		} while ( $totalReceivedObjectsCount < $totalCount );		
	}
}

class CopyPropertiesHelper
{
	static private $classPropertiesCacheMap = array();

	/**
	 * Copy all public writable (= non read-only) properties
	 * between two objects of the [exact] same class
	 * 
	 * @example $newObject = CopyPropertiesHelper::copyPublicWritableProperties( $permission, new KalturaPermission() );
	 * 
	 * @param mixed $fromObject Source object
	 * @param mixed $toObject Destination object
	 * @throws Exception in case the object types are not identical
	 * @return mixed The destination object ($toObject) 
	 */
	public static function copyPublicWritableProperties( $fromObject, $toObject )
	{
		$className = get_class( $fromObject );
		
		if ( get_class($toObject) !== $className )
		{
			throw new Exception("Object types don't match");
		}
		
		$writableProperties = self::getPublicWritableProperties( $className );
		foreach ( $writableProperties as $property )
		{
			$toObject->$property = $fromObject->$property;
		}
		
		return $toObject;
	}
	
	/**
	 * Get all public class properties, which do not contain @readonly in their php-doc comment
	 * 
	 * @param mixed $className
	 * @return array The array of public writable propoerties (or an empty array if none exist)
	 */
	private static function getPublicWritableProperties( $className )
	{
		if ( ! array_key_exists( $className, self::$classPropertiesCacheMap ) ) // First time? 
		{
			$propertiesArray = array();
			
			$r = new ReflectionClass($className);
			$props = $r->getProperties( ReflectionProperty::IS_PUBLIC & ~ReflectionProperty::IS_STATIC );
			foreach ( $props as $p )
			{
				/* @var $p ReflectionProperty */
				$comments = $p->getDocComment();
				if ( ! empty( $comments )
						&& strpos( $comments, "@readonly" ) !== false )
				{
					continue;
				}
	
				$propertiesArray[] = $p->name;
			}
			
			// Cache the results for next time
			self::$classPropertiesCacheMap[ $className ] = $propertiesArray;
		}
		else
		{
			// Get the already cached results
			$propertiesArray = self::$classPropertiesCacheMap[ $className ];
		}
		
		return $propertiesArray;
	}	
}
