# Copyright 2018 Google LLC
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

v1_library = gapic.php_library(
    service='container',
    version='v1',
    config_path='/google/container/artman_container_v1.yaml',
    artman_output_name='google-cloud-container-v1')

# copy all src including partial veneer classes
s.move(v1_library / 'src')

# copy proto files to src also
s.move(v1_library / 'proto/src/Google/Cloud/Container', 'src/')
s.move(v1_library / 'tests/')

# copy GPBMetadata file to metadata
s.move(v1_library / 'proto/src/GPBMetadata/Google/Container', 'metadata/')

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
    '**/Gapic/*GapicClient.php',
    r'Copyright \d{4}',
    'Copyright 2017')
s.replace(
    '**/V1/ClusterManagerClient.php',
    r'Copyright \d{4}',
    'Copyright 2017')
s.replace(
    'tests/**/V1/*Test.php',
    r'Copyright \d{4}',
    'Copyright 2018')

# fix documentation links
s.replace(
    '**/*.php',
    r"\(\/compute\/docs",
    '(https://cloud.google.com/compute/docs'
)

# Fix class references in gapic samples
for version in ['V1']:
    pathExpr = 'src/' + version + '/Gapic/ClusterManagerGapicClient.php'

    types = {
        'new Cluster': r'new Google\\Cloud\\Container\\'+ version + r'\\Cluster',
        'new NodePoolAutoscaling': r'new Google\\Cloud\\Container\\'+ version + r'\\NodePoolAutoscaling',
        'new AddonsConfig': r'new Google\\Cloud\\Container\\'+ version + r'\\AddonsConfig',
        '= Action::': r'= Google\\Cloud\\Container\\'+ version + r'\\SetMasterAuthRequest\\Action::',
        'new MasterAuth': r'new Google\\Cloud\\Container\\'+ version + r'\\MasterAuth',
        'new NodePool': r'new Google\\Cloud\\Container\\'+ version + r'\\NodePool',
        'new NodeManagement': r'new Google\\Cloud\\Container\\'+ version + r'\\NodeManagement',
        'new NetworkPolicy': r'new Google\\Cloud\\Container\\'+ version + r'\\NetworkPolicy',
        'new MaintenancePolicy': r'new Google\\Cloud\\Container\\'+ version + r'\\MaintenancePolicy',
    }

    for search, replace in types.items():
        s.replace(
            pathExpr,
            search,
            replace
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
