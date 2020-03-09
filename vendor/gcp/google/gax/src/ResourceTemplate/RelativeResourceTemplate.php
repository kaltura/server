<?php
/*
 * Copyright 2018 Google LLC
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

namespace Google\ApiCore\ResourceTemplate;

use Google\ApiCore\ValidationException;

/**
 * Represents a relative resource template, meaning that it will never contain a leading slash or
 * trailing verb (":<verb>").
 *
 * Examples:
 *   projects
 *   projects/{project}
 *   foo/{bar=**}/fizz/*
 *
 * Templates use the syntax of the API platform; see
 * https://github.com/googleapis/api-common-protos/blob/master/google/api/http.proto
 * for details. A template consists of a sequence of literals, wildcards, and variable bindings,
 * where each binding can have a sub-path. A string representation can be parsed into an
 * instance of AbsoluteResourceTemplate, which can then be used to perform matching and instantiation.
 */
class RelativeResourceTemplate implements ResourceTemplateInterface
{
    /** @var Segment[] $segments */
    private $segments;

    /**
     * RelativeResourceTemplate constructor.
     *
     * @param string $path
     * @throws ValidationException
     */
    public function __construct($path)
    {
        if (empty($path)) {
            $msg = sprintf(
                "Cannot construct RelativeResourceTemplate from %s string",
                is_null($path) ? "null" : "empty"
            );
            throw new ValidationException($msg);
        }
        $this->segments = Parser::parseSegments($path);

        $doubleWildcardCount = self::countDoubleWildcards($this->segments);
        if ($doubleWildcardCount > 1) {
            throw new ValidationException(
                "Cannot parse '$path': cannot contain more than one path wildcard"
            );
        }

        // Check for duplicate keys
        $keys = [];
        foreach ($this->segments as $segment) {
            if ($segment->getSegmentType() === Segment::VARIABLE_SEGMENT) {
                if (isset($keys[$segment->getKey()])) {
                    throw new ValidationException(
                        "Duplicate key '{$segment->getKey()}' in path $path"
                    );
                }
                $keys[$segment->getKey()] = true;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return implode("/", $this->segments);
    }

    /**
     * @inheritdoc
     */
    public function render(array $bindings)
    {
        $literalSegments = [];
        $keySegmentTuples = self::buildKeySegmentTuples($this->segments);
        foreach ($keySegmentTuples as list($key, $segment)) {
            /** @var Segment $segment */
            if ($segment->getSegmentType() === Segment::LITERAL_SEGMENT) {
                $literalSegments[] = $segment;
                continue;
            }
            if (!array_key_exists($key, $bindings)) {
                throw $this->renderingException($bindings, "missing required binding '$key' for segment '$segment'");
            }
            $value = $bindings[$key];
            if ($segment->matches($value)) {
                $literalSegments[] = new Segment(Segment::LITERAL_SEGMENT, $value);
            } else {
                $valueString = is_null($value) ? "null" : "'$value'";
                throw $this->renderingException(
                    $bindings,
                    "expected binding '$key' to match segment '$segment', instead got $valueString"
                );
            }
        }
        return implode("/", $literalSegments);
    }

    /**
     * @inheritdoc
     */
    public function matches($path)
    {
        try {
            $this->match($path);
            return true;
        } catch (ValidationException $ex) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function match($path)
    {
        // High level strategy for matching:
        // - Break $path into pieces based on '/'
        // - Build a list of Segments from our template, where any variable segments are
        //   flattened into a single, non-nested list
        // - Match pieces of the path with Segments in the flattened list

        $pathPieces = explode('/', $path);
        $pathPiecesCount = count($pathPieces);

        // In order to build correct bindings after we match the $path against our template, we
        // need to (a) calculate the correct positional keys for our wildcards, and (b) maintain
        // information about the variable identifier of any flattened segments. To do this, we
        // build a list of [string, Segment] tuples, where the string component is the appropriate
        // key.
        $keySegmentTuples = self::buildKeySegmentTuples($this->segments);
        $flattenedKeySegmentTuples = self::flattenKeySegmentTuples($keySegmentTuples);
        $flattenedKeySegmentTuplesCount = count($flattenedKeySegmentTuples);
        assert($flattenedKeySegmentTuplesCount > 0);

        // We would like to match pieces of our path 1:1 with the segments of our template. However,
        // this is confounded by the presence of double wildcards ('**') in the template, which can
        // match multiple segments in the path.
        // Because there can only be one '**' present, we can determine how many segments it must
        // match by examining the difference in count between the template segments and the
        // path pieces.

        if ($pathPiecesCount < $flattenedKeySegmentTuplesCount) {
            // Each segment in $flattenedKeyedSegments must consume at least one
            // segment in $pathSegments, so matching must fail.
            throw $this->matchException($path, "path does not contain enough segments to be matched");
        }

        $doubleWildcardPieceCount = $pathPiecesCount - $flattenedKeySegmentTuplesCount + 1;

        $bindings = [];
        $pathPiecesIndex = 0;
        /** @var Segment $segment */
        foreach ($flattenedKeySegmentTuples as list($segmentKey, $segment)) {
            // So long as there are tuples remaining, there will also be $pathPieces
            assert($pathPiecesIndex < $pathPiecesCount);

            // In our flattened list of segments, we should never encounter a variable segment
            assert($segment->getSegmentType() !== Segment::VARIABLE_SEGMENT);

            if ($segment->getSegmentType() == Segment::DOUBLE_WILDCARD_SEGMENT) {
                $pathPiecesForSegment = array_slice($pathPieces, $pathPiecesIndex, $doubleWildcardPieceCount);
                $pathPiece = implode('/', $pathPiecesForSegment);
                $pathPiecesIndex += $doubleWildcardPieceCount;
            } else {
                $pathPiece = $pathPieces[$pathPiecesIndex++];
            }
            if (!$segment->matches($pathPiece)) {
                throw $this->matchException($path, "expected path element matching '$segment', got '$pathPiece'");
            }

            // If we have a valid key, add our $pathPiece to the $bindings array. Note that there
            // may be multiple copies of the same $segmentKey. This is because a flattened variable
            // segment can match multiple pieces from the path. We can add these to an array and
            // collapse them all once the bindings are complete.
            if (isset($segmentKey)) {
                $bindings += [$segmentKey => []];
                $bindings[$segmentKey][] = $pathPiece;
            }
        }

        // It is possible that we have left over path pieces, which can occur if our template does
        // not have a double wildcard. In that case, the match should fail.
        if ($pathPiecesIndex !== $pathPiecesCount) {
            throw $this->matchException($path, "expected end of path, got '$pathPieces[$pathPiecesIndex]'");
        }

        // Collapse the bindings from lists into strings
        $collapsedBindings = [];
        foreach ($bindings as $key => $boundPieces) {
            $collapsedBindings[$key] = implode('/', $boundPieces);
        }
        return $collapsedBindings;
    }

    private function matchException($path, $reason)
    {
        return new ValidationException("Could not match path '$path' to template '$this': $reason");
    }

    private function renderingException($bindings, $reason)
    {
        $bindingsString = print_r($bindings, true);
        return new ValidationException(
            "Error rendering '$this': $reason\n" .
            "Provided bindings: $bindingsString"
        );
    }

    /**
     * @param Segment[] $segments
     * @return array[] A list of [string, Segment] tuples
     */
    private static function buildKeySegmentTuples(array $segments)
    {
        $keySegmentTuples = [];
        $positionalArgumentCounter = 0;
        foreach ($segments as $segment) {
            switch ($segment->getSegmentType()) {
                case Segment::WILDCARD_SEGMENT:
                case Segment::DOUBLE_WILDCARD_SEGMENT:
                    $positionalKey = "\$$positionalArgumentCounter";
                    $positionalArgumentCounter++;
                    $keySegmentTuples[] = [$positionalKey, $segment];
                    break;
                default:
                    $keySegmentTuples[] = [$segment->getKey(), $segment];
            }
        }
        return $keySegmentTuples;
    }

    /**
     * @param array[] A list of [string, Segment] tuples
     * @return array[] A list of [string, Segment] tuples
     */
    private static function flattenKeySegmentTuples(array $keySegmentTuples)
    {
        $flattenedKeySegmentTuples = [];
        foreach ($keySegmentTuples as list($key, $segment)) {
            /** @var Segment $segment */
            switch ($segment->getSegmentType()) {
                case Segment::VARIABLE_SEGMENT:
                    // For segment variables, replace the segment with the segments of its children
                    $template = $segment->getTemplate();
                    $nestedKeySegmentTuples = self::buildKeySegmentTuples($template->segments);
                    foreach ($nestedKeySegmentTuples as list($nestedKey, $nestedSegment)) {
                        /** @var Segment $nestedSegment */
                        // Nested variables are not allowed
                        assert($nestedSegment->getSegmentType() !== Segment::VARIABLE_SEGMENT);
                        // Insert the nested segment with key set to the outer key of the
                        // parent variable segment
                        $flattenedKeySegmentTuples[] = [$key, $nestedSegment];
                    }
                    break;
                default:
                    // For all other segments, don't change the key or segment
                    $flattenedKeySegmentTuples[] = [$key, $segment];
            }
        }
        return $flattenedKeySegmentTuples;
    }

    /**
     * @param Segment[] $segments
     * @return int
     */
    private static function countDoubleWildcards(array $segments)
    {
        $doubleWildcardCount = 0;
        foreach ($segments as $segment) {
            switch ($segment->getSegmentType()) {
                case Segment::DOUBLE_WILDCARD_SEGMENT:
                    $doubleWildcardCount++;
                    break;
                case Segment::VARIABLE_SEGMENT:
                    $doubleWildcardCount += self::countDoubleWildcards($segment->getTemplate()->segments);
                    break;
            }
        }
        return $doubleWildcardCount;
    }
}
