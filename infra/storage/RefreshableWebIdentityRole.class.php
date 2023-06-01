<?php

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../vendor/aws/aws-autoloader.php');

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\S3\Enum\CannedAcl;

use Aws\Common\Credentials\Credentials;
use Aws\Common\Credentials\AbstractRefreshableCredentials;
use Aws\Common\Credentials\CacheableCredentials;

use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;

class RefreshableWebIdentityRole extends AbstractRefreshableCredentials
{
        const ROLE_SESSION_NAME_PREFIX = "kaltura_s3_access_";
        const ASSUME_ROLE_CREDENTIALS_EXPIRY_TIME = 43200;

        private $roleArn = null;
        private $s3Region = null;
        private $webIdentityToken =  null;
        private $irsaRoleArn = null;

        public function refresh()
        {
                $credentialsCacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 's3_creds_cache';

                $irsaCredentials = self::assumeIrsaRole();

                $credentials = new Credentials('', '');

                $stsFactoryParams = array(
                        'credentials' => $irsaCredentials,
                );

                //Added to support regional STS endpoints in case external traffic is blocked
                if($this->s3Region)
                {
                        $stsFactoryParams['region'] = $this->s3Region;
                        $stsFactoryParams['endpoint'] = "https://sts.{$this->s3Region}.amazonaws.com";
                }

                $sts = StsClient::factory($stsFactoryParams);

                $call = $sts->AssumeRole(array(
                        'RoleArn' => $this->roleArn,
                        'RoleSessionName' => self::ROLE_SESSION_NAME_PREFIX . date('m_d_G', time()),
                        'DurationSeconds' => self::ASSUME_ROLE_CREDENTIALS_EXPIRY_TIME
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

        private function assumeIrsaRole()
        {
            $credentialsCacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 's3_creds_cache';
            $stsFactoryParams = array(
                    'profile' => 'default',
                    'version' => '2011-06-15'
            );

            if($this->s3Region)
            {
                    $stsFactoryParams['region'] = $this->s3Region;
                    $stsFactoryParams['endpoint'] = "https://sts.{$this->s3Region}.amazonaws.com";
            }

            $stsClient = StsClient::factory($stsFactoryParams);

            $result = $stsClient->AssumeRoleWithWebIdentity(array(
                    'WebIdentityToken' => $this->webIdentityToken,
                    'RoleArn' => $this->irsaRoleArn,
                    'RoleSessionName' => self::ROLE_SESSION_NAME_PREFIX . date('m_d_G', time()),
                    'DurationSeconds' => self::ASSUME_ROLE_CREDENTIALS_EXPIRY_TIME
            ));

            $baseCredentials = new Credentials($result['Credentials']['AccessKeyId'],
                    'SecretAccessKey',
                    'SessionToken',
                    strtotime($result['Credentials']['Expiration'])
                    );

            $webIdentityCache = new DoctrineCacheAdapter(new FilesystemCache("$credentialsCacheDir/webIdentityCache"));
            $webIdentityCredentials = new CacheableCredentials($baseCredentials, $webIdentityCache, 'refresh_role_creds_key');

            return $webIdentityCredentials;

        }

        public function setIRSARoleArn($irsaRoleArn)
        {
                $this->irsaRoleArn = $irsaRoleArn;
        }
        
        public function setWebIdentityToken($webIdentityToken)
        {
                $this->webIdentityToken = $webIdentityToken;
        }

        public function setRoleArn($roleArn)
        {
                $this->roleArn = $roleArn;
        }

        public function setS3Region($s3Region)
        {
                $this->s3Region = $s3Region;
        }
}
