# Copyright 2019 Google LLC
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""This script is used to synthesize generated parts of this library."""

import os
import synthtool as s
import synthtool.gcp as gcp
import logging

logging.basicConfig(level=logging.DEBUG)

gapic = gcp.GAPICGenerator()
common = gcp.CommonTemplates()

for version in ['V1', 'V1Beta1']:
    lower_version = version.lower()

    library = gapic.php_library(
        service='scheduler',
        version=lower_version,
        config_path=f'/google/cloud/scheduler/artman_cloudscheduler_{lower_version}.yaml',
        artman_output_name=f'google-cloud-cloudscheduler-{lower_version}')

    # copy all src
    s.move(library / f'src/{version}')

    # copy proto files to src also
    s.move(library / f'proto/src/Google/Cloud/Scheduler', f'src/')
    s.move(library / f'tests/')

    # copy GPBMetadata file to metadata
    s.move(library / f'proto/src/GPBMetadata/Google/Cloud/Scheduler', f'metadata/')

# document and utilize apiEndpoint instead of serviceAddress
s.replace(
    "**/Gapic/*GapicClient.php",
    r"'serviceAddress' =>",
    r"'apiEndpoint' =>")
s.replace(
    "**/Gapic/*GapicClient.php",
    r"@type string \$serviceAddress\n\s+\*\s+The address",
    r"""@type string $serviceAddress
     *           **Deprecated**. This option will be removed in a future major release. Please
     *           utilize the `$apiEndpoint` option instead.
     *     @type string $apiEndpoint
     *           The address""")
s.replace(
    "**/Gapic/*GapicClient.php",
    r"\$transportConfig, and any \$serviceAddress",
    r"$transportConfig, and any `$apiEndpoint`")

# fix year
s.replace(
    'src/**/**/*.php',
    r'Copyright \d{4}',
    r'Copyright 2019')
s.replace(
    'tests/**/**/*Test.php',
    r'Copyright \d{4}',
    r'Copyright 2019')

# V1 is GA, so remove @experimental tags
s.replace(
    'src/V1/**/*Client.php',
    r'^(\s+\*\n)?\s+\*\s@experimental\n',
    '')

# Change the wording for the deprecation warning.
s.replace(
    'src/*/*_*.php',
    r'will be removed in the next major release',
    'will be removed in a future release')

# Fix missing formatting method
s.replace(
    'src/V1/Gapic/CloudSchedulerGapicClient.php',
    r"private static \$jobNameTemplate;\n\s{4}private static \$locationNameTemplate;",
    """private static $jobNameTemplate;
    private static $locationNameTemplate;
    private static $projectNameTemplate;"""
)
s.replace(
    'src/V1/Gapic/CloudSchedulerGapicClient.php',
    "private static function getPathTemplateMap",
    """private static function getProjectNameTemplate()
    {
        if (null == self::$projectNameTemplate) {
            self::$projectNameTemplate = new PathTemplate('projects/{project}');
        }

        return self::$projectNameTemplate;
    }

    private static function getPathTemplateMap"""
)
s.replace(
    'src/V1/Gapic/CloudSchedulerGapicClient.php',
    r"'job' => self::getJobNameTemplate\(\),\n\s{0,}'location' => self::getLocationNameTemplate\(\),",
    """'job' => self::getJobNameTemplate(),
                'location' => self::getLocationNameTemplate(),
                'project' => self::getProjectNameTemplate(),"""
)
s.replace(
    'src/V1/Gapic/CloudSchedulerGapicClient.php',
    r"\/\*\*\n\s{5}\* Parses a formatted name string and returns an",
    """/**
     * Formats a string containing the fully-qualified path to represent
     * a project resource.
     *
     * @param string $project
     *
     * @return string The formatted project resource.
     */
    public static function projectName($project)
    {
        return self::getProjectNameTemplate()->render([
            'project' => $project,
        ]);
    }

    /**
     * Parses a formatted name string and returns an"""
)

### [START] protoc backwards compatibility fixes

# roll back to private properties.
s.replace(
    "src/**/V*/**/*.php",
    r"Generated from protobuf field ([^\n]{0,})\n\s{5}\*/\n\s{4}protected \$",
    r"""Generated from protobuf field \1
     */
    private $""")

# prevent proto messages from being marked final
s.replace(
    "src/**/V*/**/*.php",
    r"final class",
    r"class")

# Replace "Unwrapped" with "Value" for method names.
s.replace(
    "src/**/V*/**/*.php",
    r"public function ([s|g]\w{3,})Unwrapped",
    r"public function \1Value"
)

### [END] protoc backwards compatibility fixes
