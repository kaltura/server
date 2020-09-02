<?php

// AWS SDK PHP Client Library
require_once(KAutoloader::buildPath(KALTURA_ROOT_PATH, 'vendor', 'aws', 'aws-autoloader.php'));

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\S3\Enum\CannedAcl;

use Aws\Common\Credentials\Credentials;
use Aws\Common\Credentials\RefreshableInstanceProfileCredentials;
use Aws\Common\Credentials\AbstractRefreshableCredentials;
use Aws\Common\Credentials\CacheableCredentials;

use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;

class RefreshableRole extends AbstractRefreshableCredentials
{
	const ROLE_SESSION_NAME_PREFIX = "kaltura_s3_access_";
	const SESSION_DURATION = 3600;
	
	public function refresh()
	{
		$credentialsCacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 's3_creds_cache';
		
		$credentials = new Credentials('', '');
		$ipRefresh = new RefreshableInstanceProfileCredentials(new Credentials('', '', '', 1));
		$ipCache = new DoctrineCacheAdapter(new FilesystemCache("$credentialsCacheDir/instanceProfileCache"));
		
		$ipCreds = new CacheableCredentials($ipRefresh, $ipCache, 'refresh_role_creds_key');
		$sts = StsClient::factory(array(
			'credentials' => $ipCreds,
		));
		
		$call = $sts->assumeRole(array(
			'RoleArn' => getenv(s3Mgr::S3_ARN_ROLE_ENV_NAME),
			'RoleSessionName' => self::ROLE_SESSION_NAME_PREFIX . date('m_d_G', time()),
			'SessionDuration' => self::SESSION_DURATION,
		));
		
		$creds = $call['Credentials'];
		$result = new Credentials(
			$creds['AccessKeyId'],
			$creds['SecretAccessKey'],
			$creds['SessionToken'],
			strtotime($creds['Expiration'])
		);
		
		$this->credentials->setAccessKeyId($result->getAccessKeyId())
			->setSecretKey($result->getSecretKey())
			->setSecurityToken($result->getSecurityToken())
			->setExpiration($result->getExpiration());
		
		return $credentials;
	}
}
