<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Vision\Annotation;

/**
 * Define shared functionality for annotation features.
 * @deprecated This interface is no longer supported and will be removed in a future
 * release.
 */
interface FeatureInterface
{
    const STRENGTH_HIGH = 'high';
    const STRENGTH_MEDIUM = 'medium';
    const STRENGTH_LOW = 'low';

    /**
     * @return array
     */
    public function info();
}
