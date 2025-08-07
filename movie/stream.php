<?php
// stream.php - A more robust PHP script to serve video files with byte-range support.
// This version uses a manual loop to ensure the Content-Length header is always accurate.

// Get the file name from the query string
// Validate and sanitize the filename to prevent directory traversal attacks
if (!isset($_GET['file'])) {
    header("HTTP/1.1 400 Bad Request");
    die("Error: No file specified.");
}

$fileName = basename($_GET['file']);
// Correctly prepend the 'videos/' directory to the filename
$filePath = 'videos/' . $fileName;

// Check if the file exists and is readable
if (!file_exists($filePath) || !is_readable($filePath)) {
    header("HTTP/1.1 404 Not Found");
    die("Error: File not found or not readable.");
}

$fileSize = filesize($filePath);
$fileTime = filemtime($filePath);
$mimeType = 'video/mp4'; // Assuming MP4 for better compatibility

// Set standard headers for streaming
header('Content-Type: ' . $mimeType);
header('Cache-Control: public, must-revalidate');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileTime) . ' GMT');
header('Etag: ' . md5($fileTime . $fileSize));

// Crucial header for enabling seeking
header('Accept-Ranges: bytes');

// Check for the 'Range' header to support seeking
if (isset($_SERVER['HTTP_RANGE'])) {
    // Parse the range header, e.g., 'bytes=0-1000'
    $range = $_SERVER['HTTP_RANGE'];
    $partial = true;
    list($param, $range) = explode('=', $range);
    if (strtolower(trim($param)) != 'bytes') {
        header("HTTP/1.1 400 Bad Request");
        die();
    }

    list($start, $end) = explode('-', $range);

    $start = intval($start);
    // If the end is not specified, it's the end of the file
    if ($end === '') {
        $end = $fileSize - 1;
    } else {
        $end = intval($end);
    }

    // Ensure the range is valid
    $start = max($start, 0);
    $end = min($end, $fileSize - 1);

    $length = $end - $start + 1;

    // Respond with partial content
    header("HTTP/1.1 206 Partial Content");
    header("Content-Range: bytes " . $start . "-" . $end . "/" . $fileSize);
    header("Content-Length: " . $length);

    $fh = fopen($filePath, 'rb');
    fseek($fh, $start);
    $buffer = 1024 * 16; // 16kb buffer
    $bytesLeft = $length;
    while (!feof($fh) && $bytesLeft > 0) {
        $bytesToRead = min($bytesLeft, $buffer);
        echo fread($fh, $bytesToRead);
        $bytesLeft -= $bytesToRead;
        // Flush the buffer to send data in chunks
        flush();
    }
    fclose($fh);

} else {
    // No range header, send the whole file
    header("HTTP/1.1 200 OK");
    header('Content-Length: ' . $fileSize);
    readfile($filePath);
}
?>
