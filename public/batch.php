<?php
declare(strict_types=1);

const MAX_REQUEST_BYTES = 1048576;
const MAX_RUNS = 1000;
const MAX_RESPONSE_BYTES = 67108864;

function fail(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail(405, 'POST required');
}

$length = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($length < 0 || $length > MAX_REQUEST_BYTES) {
    fail(413, 'request too large');
}

$body = file_get_contents('php://input');
if ($body === false || strlen($body) > MAX_REQUEST_BYTES) {
    fail(413, 'request too large');
}

try {
    $runs = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    fail(400, 'invalid JSON');
}

if (!is_array($runs) || count($runs) > MAX_RUNS) {
    fail(400, 'invalid batch');
}

$root = realpath('/mirror');
$dataRoot = $root === false ? false : realpath($root . DIRECTORY_SEPARATOR . 'data');
if ($root === false || $dataRoot === false || !is_dir($dataRoot)) {
    fail(500, 'data root is unavailable');
}

$dataRootPrefix = rtrim($dataRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$parts = [];
$lengths = [];
$total = 0;

foreach ($runs as $run) {
    if (!is_array($run) || count($run) !== 3) {
        fail(400, 'invalid batch item');
    }

    [$name, $start, $end] = $run;
    if (!is_string($name) || str_contains($name, "\0") || str_contains($name, '\\')) {
        fail(400, 'invalid file path');
    }
    if (!is_int($start) || !is_int($end) || $start < 0 || $end < $start) {
        fail(400, 'invalid file range');
    }

    $file = realpath($dataRoot . DIRECTORY_SEPARATOR . ltrim($name, '/'));
    if ($file === false || !str_starts_with($file, $dataRootPrefix) || !is_file($file)) {
        fail(400, 'invalid file path');
    }

    $size = filesize($file);
    if ($size === false) {
        fail(400, 'could not stat file');
    }

    $offset = min($start, $size);
    $limit = min($end + 1, $size);
    $count = max(0, $limit - $offset);
    $total += $count;
    if ($total > MAX_RESPONSE_BYTES) {
        fail(400, 'batch exceeds 64 MiB');
    }

    if ($count === 0) {
        $parts[] = '';
        $lengths[] = 0;
        continue;
    }

    $handle = fopen($file, 'rb');
    if ($handle === false || fseek($handle, $offset) !== 0) {
        if (is_resource($handle)) {
            fclose($handle);
        }
        fail(400, 'could not read file');
    }

    $part = '';
    while (strlen($part) < $count) {
        $chunk = fread($handle, min(1048576, $count - strlen($part)));
        if ($chunk === false || $chunk === '') {
            fclose($handle);
            fail(400, 'short file read');
        }
        $part .= $chunk;
    }
    fclose($handle);
    $parts[] = $part;
    $lengths[] = strlen($part);
}

$response = implode('', $parts);
if (($_GET['gz'] ?? '') === '1') {
    $compressed = gzencode($response, 1);
    if ($compressed === false) {
        fail(500, 'gzip failed');
    }
    $response = $compressed;
    header('Content-Encoding: gzip');
}

header('Content-Type: application/octet-stream');
header('Content-Length: ' . strlen($response));
header('X-Run-Lengths: ' . implode(',', $lengths));
echo $response;
