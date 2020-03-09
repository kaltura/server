<?php
/*
 * Copyright 2017 Google LLC
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following disclaimer
 * in the documentation and/or other materials provided with the
 * distribution.
 *     * Neither the name of Google Inc. nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Google\ApiCore;

/**
 * Encapsulates request params header metadata.
 */
class RequestParamsHeaderDescriptor
{
    const HEADER_KEY = 'x-goog-request-params';

    /**
     * @var array
     */
    private $header;

    /**
     * RequestParamsHeaderDescriptor constructor.
     *
     * @param array $requestParams An associative array which contains request params header data in
     * a form ['field_name.subfield_name' => value].
     */
    public function __construct($requestParams)
    {
        $headerKey = self::HEADER_KEY;

        $headerValue = '';
        foreach ($requestParams as $key => $value) {
            if ('' !== $headerValue) {
                $headerValue .= '&';
            }
            $headerValue .= $key . '=' . strval($value);
        }

        // If the value contains non-ASCII characters, suffix the header key with `-bin`.
        // see https://grpc.github.io/grpc/python/glossary.html#term-metadata
        if (preg_match('/[^\x00-\x7F]/', $headerValue) !== 0) {
            $headerKey = $headerKey . '-bin';
        }

        $this->header = [$headerKey => [$headerValue]];
    }

    /**
     * Returns an associative array that contains request params header metadata.
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }
}
