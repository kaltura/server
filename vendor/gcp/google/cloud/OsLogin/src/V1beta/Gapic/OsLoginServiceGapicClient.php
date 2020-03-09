<?php
/*
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * GENERATED CODE WARNING
 * This file was generated from the file
 * https://github.com/google/googleapis/blob/master/google/cloud/oslogin/v1beta/oslogin.proto
 * and updates to that file get reflected here through a refresh process.
 *
 * @experimental
 */

namespace Google\Cloud\OsLogin\V1beta\Gapic;

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\GapicClientTrait;
use Google\ApiCore\PathTemplate;
use Google\ApiCore\RequestParamsHeaderDescriptor;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\Auth\FetchAuthTokenInterface;
use Google\Cloud\OsLogin\Common\SshPublicKey;
use Google\Cloud\OsLogin\V1beta\DeletePosixAccountRequest;
use Google\Cloud\OsLogin\V1beta\DeleteSshPublicKeyRequest;
use Google\Cloud\OsLogin\V1beta\GetLoginProfileRequest;
use Google\Cloud\OsLogin\V1beta\GetSshPublicKeyRequest;
use Google\Cloud\OsLogin\V1beta\ImportSshPublicKeyRequest;
use Google\Cloud\OsLogin\V1beta\ImportSshPublicKeyResponse;
use Google\Cloud\OsLogin\V1beta\LoginProfile;
use Google\Cloud\OsLogin\V1beta\UpdateSshPublicKeyRequest;
use Google\Protobuf\FieldMask;
use Google\Protobuf\GPBEmpty;

/**
 * Service Description: Cloud OS Login API.
 *
 * The Cloud OS Login API allows you to manage users and their associated SSH
 * public keys for logging into virtual machines on Google Cloud Platform.
 *
 * This class provides the ability to make remote calls to the backing service through method
 * calls that map to API methods. Sample code to get started:
 *
 * ```
 * $osLoginServiceClient = new OsLoginServiceClient();
 * try {
 *     $formattedName = $osLoginServiceClient->projectName('[USER]', '[PROJECT]');
 *     $osLoginServiceClient->deletePosixAccount($formattedName);
 * } finally {
 *     $osLoginServiceClient->close();
 * }
 * ```
 *
 * Many parameters require resource names to be formatted in a particular way. To assist
 * with these names, this class includes a format method for each type of name, and additionally
 * a parseName method to extract the individual identifiers contained within formatted names
 * that are returned by the API.
 *
 * @experimental
 */
class OsLoginServiceGapicClient
{
    use GapicClientTrait;

    /**
     * The name of the service.
     */
    const SERVICE_NAME = 'google.cloud.oslogin.v1beta.OsLoginService';

    /**
     * The default address of the service.
     */
    const SERVICE_ADDRESS = 'oslogin.googleapis.com';

    /**
     * The default port of the service.
     */
    const DEFAULT_SERVICE_PORT = 443;

    /**
     * The name of the code generator, to be included in the agent header.
     */
    const CODEGEN_NAME = 'gapic';

    /**
     * The default scopes required by the service.
     */
    public static $serviceScopes = [
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/cloud-platform.read-only',
        'https://www.googleapis.com/auth/compute',
        'https://www.googleapis.com/auth/compute.readonly',
    ];
    private static $fingerprintNameTemplate;
    private static $projectNameTemplate;
    private static $userNameTemplate;
    private static $pathTemplateMap;

    private static function getClientDefaults()
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' => self::SERVICE_ADDRESS.':'.self::DEFAULT_SERVICE_PORT,
            'clientConfig' => __DIR__.'/../resources/os_login_service_client_config.json',
            'descriptorsConfigPath' => __DIR__.'/../resources/os_login_service_descriptor_config.php',
            'gcpApiConfigPath' => __DIR__.'/../resources/os_login_service_grpc_config.json',
            'credentialsConfig' => [
                'scopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' => __DIR__.'/../resources/os_login_service_rest_client_config.php',
                ],
            ],
        ];
    }

    private static function getFingerprintNameTemplate()
    {
        if (null == self::$fingerprintNameTemplate) {
            self::$fingerprintNameTemplate = new PathTemplate('users/{user}/sshPublicKeys/{fingerprint}');
        }

        return self::$fingerprintNameTemplate;
    }

    private static function getProjectNameTemplate()
    {
        if (null == self::$projectNameTemplate) {
            self::$projectNameTemplate = new PathTemplate('users/{user}/projects/{project}');
        }

        return self::$projectNameTemplate;
    }

    private static function getUserNameTemplate()
    {
        if (null == self::$userNameTemplate) {
            self::$userNameTemplate = new PathTemplate('users/{user}');
        }

        return self::$userNameTemplate;
    }

    private static function getPathTemplateMap()
    {
        if (null == self::$pathTemplateMap) {
            self::$pathTemplateMap = [
                'fingerprint' => self::getFingerprintNameTemplate(),
                'project' => self::getProjectNameTemplate(),
                'user' => self::getUserNameTemplate(),
            ];
        }

        return self::$pathTemplateMap;
    }

    /**
     * Formats a string containing the fully-qualified path to represent
     * a fingerprint resource.
     *
     * @param string $user
     * @param string $fingerprint
     *
     * @return string The formatted fingerprint resource.
     * @experimental
     */
    public static function fingerprintName($user, $fingerprint)
    {
        return self::getFingerprintNameTemplate()->render([
            'user' => $user,
            'fingerprint' => $fingerprint,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent
     * a project resource.
     *
     * @param string $user
     * @param string $project
     *
     * @return string The formatted project resource.
     * @experimental
     */
    public static function projectName($user, $project)
    {
        return self::getProjectNameTemplate()->render([
            'user' => $user,
            'project' => $project,
        ]);
    }

    /**
     * Formats a string containing the fully-qualified path to represent
     * a user resource.
     *
     * @param string $user
     *
     * @return string The formatted user resource.
     * @experimental
     */
    public static function userName($user)
    {
        return self::getUserNameTemplate()->render([
            'user' => $user,
        ]);
    }

    /**
     * Parses a formatted name string and returns an associative array of the components in the name.
     * The following name formats are supported:
     * Template: Pattern
     * - fingerprint: users/{user}/sshPublicKeys/{fingerprint}
     * - project: users/{user}/projects/{project}
     * - user: users/{user}.
     *
     * The optional $template argument can be supplied to specify a particular pattern, and must
     * match one of the templates listed above. If no $template argument is provided, or if the
     * $template argument does not match one of the templates listed, then parseName will check
     * each of the supported templates, and return the first match.
     *
     * @param string $formattedName The formatted name string
     * @param string $template      Optional name of template to match
     *
     * @return array An associative array from name component IDs to component values.
     *
     * @throws ValidationException If $formattedName could not be matched.
     * @experimental
     */
    public static function parseName($formattedName, $template = null)
    {
        $templateMap = self::getPathTemplateMap();

        if ($template) {
            if (!isset($templateMap[$template])) {
                throw new ValidationException("Template name $template does not exist");
            }

            return $templateMap[$template]->match($formattedName);
        }

        foreach ($templateMap as $templateName => $pathTemplate) {
            try {
                return $pathTemplate->match($formattedName);
            } catch (ValidationException $ex) {
                // Swallow the exception to continue trying other path templates
            }
        }
        throw new ValidationException("Input did not match any known format. Input: $formattedName");
    }

    /**
     * Constructor.
     *
     * @param array $options {
     *                       Optional. Options for configuring the service API wrapper.
     *
     *     @type string $serviceAddress
     *           **Deprecated**. This option will be removed in a future major release. Please
     *           utilize the `$apiEndpoint` option instead.
     *     @type string $apiEndpoint
     *           The address of the API remote host. May optionally include the port, formatted
     *           as "<uri>:<port>". Default 'oslogin.googleapis.com:443'.
     *     @type string|array|FetchAuthTokenInterface|CredentialsWrapper $credentials
     *           The credentials to be used by the client to authorize API calls. This option
     *           accepts either a path to a credentials file, or a decoded credentials file as a
     *           PHP array.
     *           *Advanced usage*: In addition, this option can also accept a pre-constructed
     *           {@see \Google\Auth\FetchAuthTokenInterface} object or
     *           {@see \Google\ApiCore\CredentialsWrapper} object. Note that when one of these
     *           objects are provided, any settings in $credentialsConfig will be ignored.
     *     @type array $credentialsConfig
     *           Options used to configure credentials, including auth token caching, for the client.
     *           For a full list of supporting configuration options, see
     *           {@see \Google\ApiCore\CredentialsWrapper::build()}.
     *     @type bool $disableRetries
     *           Determines whether or not retries defined by the client configuration should be
     *           disabled. Defaults to `false`.
     *     @type string|array $clientConfig
     *           Client method configuration, including retry settings. This option can be either a
     *           path to a JSON file, or a PHP array containing the decoded JSON data.
     *           By default this settings points to the default client config file, which is provided
     *           in the resources folder.
     *     @type string|TransportInterface $transport
     *           The transport used for executing network requests. May be either the string `rest`
     *           or `grpc`. Defaults to `grpc` if gRPC support is detected on the system.
     *           *Advanced usage*: Additionally, it is possible to pass in an already instantiated
     *           {@see \Google\ApiCore\Transport\TransportInterface} object. Note that when this
     *           object is provided, any settings in $transportConfig, and any `$apiEndpoint`
     *           setting, will be ignored.
     *     @type array $transportConfig
     *           Configuration options that will be used to construct the transport. Options for
     *           each supported transport type should be passed in a key for that transport. For
     *           example:
     *           $transportConfig = [
     *               'grpc' => [...],
     *               'rest' => [...]
     *           ];
     *           See the {@see \Google\ApiCore\Transport\GrpcTransport::build()} and
     *           {@see \Google\ApiCore\Transport\RestTransport::build()} methods for the
     *           supported options.
     * }
     *
     * @throws ValidationException
     * @experimental
     */
    public function __construct(array $options = [])
    {
        $clientOptions = $this->buildClientOptions($options);
        $this->setClientOptions($clientOptions);
    }

    /**
     * Deletes a POSIX account.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedName = $osLoginServiceClient->projectName('[USER]', '[PROJECT]');
     *     $osLoginServiceClient->deletePosixAccount($formattedName);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string $name         Required. A reference to the POSIX account to update. POSIX accounts are identified
     *                             by the project ID they are associated with. A reference to the POSIX
     *                             account is in format `users/{user}/projects/{project}`.
     * @param array  $optionalArgs {
     *                             Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function deletePosixAccount($name, array $optionalArgs = [])
    {
        $request = new DeletePosixAccountRequest();
        $request->setName($name);

        $requestParams = new RequestParamsHeaderDescriptor([
          'name' => $request->getName(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'DeletePosixAccount',
            GPBEmpty::class,
            $optionalArgs,
            $request
        )->wait();
    }

    /**
     * Deletes an SSH public key.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedName = $osLoginServiceClient->fingerprintName('[USER]', '[FINGERPRINT]');
     *     $osLoginServiceClient->deleteSshPublicKey($formattedName);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string $name         Required. The fingerprint of the public key to update. Public keys are identified by
     *                             their SHA-256 fingerprint. The fingerprint of the public key is in format
     *                             `users/{user}/sshPublicKeys/{fingerprint}`.
     * @param array  $optionalArgs {
     *                             Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function deleteSshPublicKey($name, array $optionalArgs = [])
    {
        $request = new DeleteSshPublicKeyRequest();
        $request->setName($name);

        $requestParams = new RequestParamsHeaderDescriptor([
          'name' => $request->getName(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'DeleteSshPublicKey',
            GPBEmpty::class,
            $optionalArgs,
            $request
        )->wait();
    }

    /**
     * Retrieves the profile information used for logging in to a virtual machine
     * on Google Compute Engine.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedName = $osLoginServiceClient->userName('[USER]');
     *     $response = $osLoginServiceClient->getLoginProfile($formattedName);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string $name         Required. The unique ID for the user in format `users/{user}`.
     * @param array  $optionalArgs {
     *                             Optional.
     *
     *     @type string $projectId
     *          The project ID of the Google Cloud Platform project.
     *     @type string $systemId
     *          A system ID for filtering the results of the request.
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Google\Cloud\OsLogin\V1beta\LoginProfile
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function getLoginProfile($name, array $optionalArgs = [])
    {
        $request = new GetLoginProfileRequest();
        $request->setName($name);
        if (isset($optionalArgs['projectId'])) {
            $request->setProjectId($optionalArgs['projectId']);
        }
        if (isset($optionalArgs['systemId'])) {
            $request->setSystemId($optionalArgs['systemId']);
        }

        $requestParams = new RequestParamsHeaderDescriptor([
          'name' => $request->getName(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'GetLoginProfile',
            LoginProfile::class,
            $optionalArgs,
            $request
        )->wait();
    }

    /**
     * Retrieves an SSH public key.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedName = $osLoginServiceClient->fingerprintName('[USER]', '[FINGERPRINT]');
     *     $response = $osLoginServiceClient->getSshPublicKey($formattedName);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string $name         Required. The fingerprint of the public key to retrieve. Public keys are identified
     *                             by their SHA-256 fingerprint. The fingerprint of the public key is in
     *                             format `users/{user}/sshPublicKeys/{fingerprint}`.
     * @param array  $optionalArgs {
     *                             Optional.
     *
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Google\Cloud\OsLogin\Common\SshPublicKey
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function getSshPublicKey($name, array $optionalArgs = [])
    {
        $request = new GetSshPublicKeyRequest();
        $request->setName($name);

        $requestParams = new RequestParamsHeaderDescriptor([
          'name' => $request->getName(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'GetSshPublicKey',
            SshPublicKey::class,
            $optionalArgs,
            $request
        )->wait();
    }

    /**
     * Adds an SSH public key and returns the profile information. Default POSIX
     * account information is set when no username and UID exist as part of the
     * login profile.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedParent = $osLoginServiceClient->userName('[USER]');
     *     $sshPublicKey = new SshPublicKey();
     *     $response = $osLoginServiceClient->importSshPublicKey($formattedParent, $sshPublicKey);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string       $parent       The unique ID for the user in format `users/{user}`.
     * @param SshPublicKey $sshPublicKey Required. The SSH public key and expiration time.
     * @param array        $optionalArgs {
     *                                   Optional.
     *
     *     @type string $projectId
     *          The project ID of the Google Cloud Platform project.
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Google\Cloud\OsLogin\V1beta\ImportSshPublicKeyResponse
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function importSshPublicKey($parent, $sshPublicKey, array $optionalArgs = [])
    {
        $request = new ImportSshPublicKeyRequest();
        $request->setParent($parent);
        $request->setSshPublicKey($sshPublicKey);
        if (isset($optionalArgs['projectId'])) {
            $request->setProjectId($optionalArgs['projectId']);
        }

        $requestParams = new RequestParamsHeaderDescriptor([
          'parent' => $request->getParent(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'ImportSshPublicKey',
            ImportSshPublicKeyResponse::class,
            $optionalArgs,
            $request
        )->wait();
    }

    /**
     * Updates an SSH public key and returns the profile information. This method
     * supports patch semantics.
     *
     * Sample code:
     * ```
     * $osLoginServiceClient = new OsLoginServiceClient();
     * try {
     *     $formattedName = $osLoginServiceClient->fingerprintName('[USER]', '[FINGERPRINT]');
     *     $sshPublicKey = new SshPublicKey();
     *     $response = $osLoginServiceClient->updateSshPublicKey($formattedName, $sshPublicKey);
     * } finally {
     *     $osLoginServiceClient->close();
     * }
     * ```
     *
     * @param string       $name         Required. The fingerprint of the public key to update. Public keys are identified by
     *                                   their SHA-256 fingerprint. The fingerprint of the public key is in format
     *                                   `users/{user}/sshPublicKeys/{fingerprint}`.
     * @param SshPublicKey $sshPublicKey Required. The SSH public key and expiration time.
     * @param array        $optionalArgs {
     *                                   Optional.
     *
     *     @type FieldMask $updateMask
     *          Mask to control which fields get updated. Updates all if not present.
     *     @type RetrySettings|array $retrySettings
     *          Retry settings to use for this call. Can be a
     *          {@see Google\ApiCore\RetrySettings} object, or an associative array
     *          of retry settings parameters. See the documentation on
     *          {@see Google\ApiCore\RetrySettings} for example usage.
     * }
     *
     * @return \Google\Cloud\OsLogin\Common\SshPublicKey
     *
     * @throws ApiException if the remote call fails
     * @experimental
     */
    public function updateSshPublicKey($name, $sshPublicKey, array $optionalArgs = [])
    {
        $request = new UpdateSshPublicKeyRequest();
        $request->setName($name);
        $request->setSshPublicKey($sshPublicKey);
        if (isset($optionalArgs['updateMask'])) {
            $request->setUpdateMask($optionalArgs['updateMask']);
        }

        $requestParams = new RequestParamsHeaderDescriptor([
          'name' => $request->getName(),
        ]);
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'UpdateSshPublicKey',
            SshPublicKey::class,
            $optionalArgs,
            $request
        )->wait();
    }
}
