<?php
/**
 * Standalone test for disposition-aware audio track matching.
 *
 * Tests the KDL stream selection logic against the actual audio tracks present
 * in the sample file, as parsed by KFFMpegMediaParser.
 *
 * Source file streams (from ffprobe):
 *   Index 1 - eng, default=1               → standard English
 *   Index 2 - eng, visual_impaired=1        → audio description
 *   Index 3 - fra, default=1               → French
 *   Index 4 - spa, default=1               → Spanish
 *
 * Usage:  php tests/test_audio_disposition_matching.php
 *         php tests/test_audio_disposition_matching.php --with-parser
 */

define('KALTURA_ROOT_PATH', __DIR__ . '/..');

// ---------------------------------------------------------------------------
// Stubs for platform classes not needed by the KDL
// ---------------------------------------------------------------------------
class KalturaLog {
    public static function log($msg)  {}
    public static function info($msg) {}
    public static function err($msg)  { echo "  [KalturaLog::err] $msg\n"; }
}

class KalturaObject {}

// Minimal KalturaMediaInfo — only the dynamic properties used by the KDL
class KalturaMediaInfo extends KalturaObject {
    public $id;
    public $codecType;
    public $audioFormat;
    public $audioCodecId;
    public $audioDuration;
    public $audioBitRate;
    public $audioBitRateMode;
    public $audioChannels;
    public $audioChannelLayout;
    public $audioSamplingRate;
    public $audioResolution;
    public $audioLanguage;
    public $audioDisposition; // array of active flags, added by our change
    public $containerProfile;
    public $extradata;
    public $contentStreams;
}

// ---------------------------------------------------------------------------
// Load KDL files
// ---------------------------------------------------------------------------
require_once KALTURA_ROOT_PATH . '/infra/cdl/kdl/KDLCommon.php';
require_once KALTURA_ROOT_PATH . '/infra/cdl/kdl/KDLMediaObjectData.php';
require_once KALTURA_ROOT_PATH . '/infra/cdl/kdl/KDLMediaDataSet.php';
require_once KALTURA_ROOT_PATH . '/infra/cdl/kdl/KDLAudioMultiStreaming.php';

// ---------------------------------------------------------------------------
// Optionally test the parser directly against the real file
// ---------------------------------------------------------------------------
$withParser = in_array('--with-parser', $argv ?? []);
if ($withParser) {
    require_once KALTURA_ROOT_PATH . '/infra/media/mediaInfoParser/KBaseMediaParser.php';
    require_once KALTURA_ROOT_PATH . '/infra/media/mediaInfoParser/KFFMpegMediaParser.php';
}

// ---------------------------------------------------------------------------
// Helper — build contentStreams from the ffprobe output of the sample file,
// simulating what KFFMpegMediaParser::parseAudioStream() produces.
// This mirrors the exact ffprobe output for the test.mp4 sample.
// ---------------------------------------------------------------------------
function buildContentStreamsFromSample(): stdClass
{
    $streams = new stdClass();
    $streams->audio = [];

    $rawAudioStreams = [
        // index, language, channels, sampleRate, disposition flags active
        [1, 'eng', 2, 48000, ['default']],
        [2, 'eng', 2, 48000, ['visual_impaired', 'descriptions']],
        [3, 'fra', 2, 48000, ['default']],
        [4, 'spa', 2, 48000, ['default']],
    ];

    foreach ($rawAudioStreams as [$index, $lang, $channels, $sampleRate, $dispositions]) {
        $stream = new KalturaMediaInfo();
        $stream->id             = $index;
        $stream->codecType      = 'audio';
        $stream->audioFormat    = 'aac';
        $stream->audioCodecId   = 'mp4a';
        $stream->audioChannels  = $channels;
        $stream->audioSamplingRate = $sampleRate;
        $stream->audioDuration  = 900011; // ms
        $stream->audioBitRate   = 224;
        $stream->audioLanguage  = $lang;
        if (!empty($dispositions)) {
            $stream->audioDisposition = $dispositions;
        }
        $streams->audio[] = $stream;
    }

    return $streams;
}

// ---------------------------------------------------------------------------
// Helper — build the audio portion of a multiStream config (as KDLFlavor.php
// extracts $target->_multiStream->audio before passing to the helper).
// Equivalent to parsing: {"audio":{"languages":["eng"]}} and taking ->audio
// ---------------------------------------------------------------------------
function makeMultiStreamAudioConfig(string $lang): stdClass
{
    // This is what $setupMultiStream looks like inside evaluateTargetAudioMultiStream()
    $audio = new stdClass();
    $audio->languages = [$lang];
    return $audio;
}

// ---------------------------------------------------------------------------
// Test runner
// ---------------------------------------------------------------------------
$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void
{
    global $passed, $failed;
    if ($condition) {
        echo "  \033[32m✓\033[0m $name\n";
        $passed++;
    } else {
        echo "  \033[31m✗\033[0m $name" . ($detail ? " — $detail" : '') . "\n";
        $failed++;
    }
}

// ===========================================================================
// PART 1 — Parser output validation (optional, requires --with-parser)
// ===========================================================================
if ($withParser) {
    echo "\n=== Part 1: KFFMpegMediaParser disposition parsing ===\n";

    $sampleFile = 'C:\\Users\\thomas.ellis\\Kaltura Dropbox\\Thomas Ellis\\Criterion Examples\\test.mp4';

    try {
        $parser    = new KFFMpegMediaParser($sampleFile);
        $mediaInfo = $parser->getMediaInfo();

        $audioStreams = $mediaInfo->contentStreams['audio'] ?? [];
        assert_test('Parser found 4 audio streams', count($audioStreams) === 4,
            'found ' . count($audioStreams));

        $engDefault = $audioStreams[0] ?? null;
        assert_test('Stream 1: language=eng',
            isset($engDefault) && $engDefault->audioLanguage === 'eng');
        assert_test('Stream 1: disposition=[default]',
            isset($engDefault->audioDisposition) && in_array('default', $engDefault->audioDisposition));
        assert_test('Stream 1: NOT visual_impaired',
            !isset($engDefault->audioDisposition) || !in_array('visual_impaired', $engDefault->audioDisposition));

        $engAD = $audioStreams[1] ?? null;
        assert_test('Stream 2: language=eng',
            isset($engAD) && $engAD->audioLanguage === 'eng');
        assert_test('Stream 2: disposition contains visual_impaired',
            isset($engAD->audioDisposition) && in_array('visual_impaired', $engAD->audioDisposition));

        $fra = $audioStreams[2] ?? null;
        assert_test('Stream 3: language=fra', isset($fra) && $fra->audioLanguage === 'fra');

        $spa = $audioStreams[3] ?? null;
        assert_test('Stream 4: language=spa', isset($spa) && $spa->audioLanguage === 'spa');

    } catch (Exception $e) {
        echo "  \033[31m✗\033[0m Parser threw: " . $e->getMessage() . "\n";
        $failed++;
    }
}

// ===========================================================================
// PART 2 — KDL stream selection logic
// ===========================================================================
echo "\n=== Part 2: KDL disposition-aware stream selection ===\n";

$contentStreams = buildContentStreamsFromSample();

// --- Scenario 1: Standard English flavor (no audio_description tag) --------
echo "\nScenario 1 — Standard English flavor (tags: 'mobile,web,alt_audio')\n";
{
    $helper      = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('eng'));
    $flavorTags  = 'mobile,web,mbr,iphone,audio_only,alt_audio';
    $result      = $helper->GetSettings($contentStreams, null, 1, $flavorTags);

    assert_test('Returns a result', isset($result));
    $mapping = $result ? $result->getStreamMapping(0) : null;
    assert_test('Maps to stream index 1 (eng default)',
        isset($mapping) && in_array(1, (array)$mapping),
        'mapped to: ' . json_encode($mapping));
    assert_test('Does NOT map to stream index 2 (audio description)',
        !isset($mapping) || !in_array(2, (array)$mapping));
}

// --- Scenario 2: Audio Description flavor (audio_description tag) ----------
echo "\nScenario 2 — Audio Description flavor (tags: 'mobile,web,audio_only,audio_description')\n";
{
    $helper      = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('eng'));
    $flavorTags  = 'mobile,web,mbr,iphone,audio_only,alt_audio,audio_description,dash';
    $result      = $helper->GetSettings($contentStreams, null, 1, $flavorTags);

    assert_test('Returns a result', isset($result));
    $mapping = $result ? $result->getStreamMapping(0) : null;
    assert_test('Maps to stream index 2 (eng visual_impaired)',
        isset($mapping) && in_array(2, (array)$mapping),
        'mapped to: ' . json_encode($mapping));
    assert_test('Does NOT map to stream index 1 (default eng)',
        !isset($mapping) || !in_array(1, (array)$mapping));
}

// --- Scenario 3: French flavor (no audio_description tag) ------------------
echo "\nScenario 3 — French flavor (tags: 'mobile,web,alt_audio')\n";
{
    $helper      = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('fra'));
    $flavorTags  = 'mobile,web,mbr,iphone,audio_only,alt_audio';
    $result      = $helper->GetSettings($contentStreams, null, 1, $flavorTags);

    assert_test('Returns a result', isset($result));
    $mapping = $result ? $result->getStreamMapping(0) : null;
    assert_test('Maps to stream index 3 (fra)',
        isset($mapping) && in_array(3, (array)$mapping),
        'mapped to: ' . json_encode($mapping));
}

// --- Scenario 4: Spanish flavor (no audio_description tag) -----------------
echo "\nScenario 4 — Spanish flavor (tags: 'mobile,web,alt_audio')\n";
{
    $helper      = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('spa'));
    $flavorTags  = 'mobile,web,mbr,iphone,audio_only,alt_audio';
    $result      = $helper->GetSettings($contentStreams, null, 1, $flavorTags);

    assert_test('Returns a result', isset($result));
    $mapping = $result ? $result->getStreamMapping(0) : null;
    assert_test('Maps to stream index 4 (spa)',
        isset($mapping) && in_array(4, (array)$mapping),
        'mapped to: ' . json_encode($mapping));
}

// --- Scenario 5: Audio Description flavor with no matching source track -----
echo "\nScenario 5 — Audio Description flavor, language with no AD track (fra)\n";
{
    $helper      = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('fra'));
    $flavorTags  = 'mobile,web,audio_only,audio_description,dash';
    $result      = $helper->GetSettings($contentStreams, null, 1, $flavorTags);

    // fra only has one track with no visual_impaired disposition — should return null
    assert_test('Returns null (no AD track exists for fra)',
        !isset($result) || $result->getStreamMapping(0) === null);
}

// --- Scenario 6: Single eng track (no AD track in source) ------------------
echo "\nScenario 6 — Single-language source, standard flavor (backward compat)\n";
{
    // Build a minimal content streams with only one eng track (no AD)
    $singleStream = new stdClass();
    $singleStream->audio = [];
    $only = new KalturaMediaInfo();
    $only->id              = 1;
    $only->audioLanguage   = 'eng';
    $only->audioChannels   = 2;
    $only->audioDuration   = 900011;
    $only->audioDisposition = ['default'];
    $singleStream->audio[] = $only;

    $helper     = new KDLAudioMultiStreamingHelper(makeMultiStreamAudioConfig('eng'));
    $flavorTags = 'mobile,web,alt_audio';
    $result     = $helper->GetSettings($singleStream, null, 1, $flavorTags);

    assert_test('Returns a result (single eng track, standard flavor)', isset($result));
    $mapping = $result ? $result->getStreamMapping(0) : null;
    assert_test('Maps to stream index 1', isset($mapping) && in_array(1, (array)$mapping),
        'mapped to: ' . json_encode($mapping));
}

// ===========================================================================
// Summary
// ===========================================================================
$total = $passed + $failed;
echo "\n" . str_repeat('─', 50) . "\n";
echo "Results: $passed/$total passed";
if ($failed > 0) {
    echo " \033[31m($failed failed)\033[0m";
} else {
    echo " \033[32m(all passed)\033[0m";
}
echo "\n\n";

exit($failed > 0 ? 1 : 0);
