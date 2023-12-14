<?php

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../vendor/aws/aws-autoloader.php');

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\DoctrineCacheAdapter;
use Doctrine\Common\Cache\FilesystemCache;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\InstanceProfileProvider;
use Aws\Credentials\AssumeRoleCredentialProvider;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;

class RefreshableRole
{
	const ROLE_SESSION_NAME_PREFIX = "kaltura_s3_access_";
	const ASSUME_ROLE_CREDENTIALS_EXPIRY_TIME = 43200;

	public static function getCacheCredentialsProvider($roleArn, $s3Region = null)
	{
		$credentialsCacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 's3_creds_cache_v3';

		$profile = new InstanceProfileProvider();
		$cache = new DoctrineCacheAdapter(new FilesystemCache($credentialsCacheDir));

		$stsArgs = array(
			'version' => '2011-06-15',
			'credentials' => $profile,
		);

		//Added to support regional STS endpoints in case external traffic is blocked
		if($s3Region)
		{
			$stsArgs['region'] = $s3Region;
			$stsArgs['endpoint'] = "https://sts.{$s3Region}.amazonaws.com";
		}

		$provider = new AssumeRoleCredentialProvider(array(
			'client' => new StsClient($stsArgs),
			'assume_role_params' => array(
				'RoleArn' => $roleArn,
				'RoleSessionName' => self::ROLE_SESSION_NAME_PREFIX . date('m_d_G', time()),
				'DurationSeconds' => self::ASSUME_ROLE_CREDENTIALS_EXPIRY_TIME
			),
		));

		return CredentialProvider::cache($provider, $cache);
	}
}