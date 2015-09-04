<?php

namespace MathieuImbert\SimpleEmailParser;

class Parser
{

    public $rawData;
    public $rawHeaders;
    public $rawBody;
    public $headers;

    public $bodyText = '';
    public $bodyHtml = '';


    public function __construct()
    {
    }

    public function loadContent($content)
    {
        $this->rawData = $content;
    }

    public function loadFile($file)
    {

        $fileContent = file_get_contents($file);
        $this->loadContent($fileContent);
    }

    /**
     * Return all headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return a specific header
     *
     * @param $key
     * @return string|bool
     */
    public function getHeader($key)
    {
        $key = strtolower($key);
        return isset($this->headers[$key]) ? $this->headers[$key] : false;
    }

    public function parse()
    {

        $array = preg_split('/\R{2}/', $this->rawData, 2);

        $this->rawHeaders = trim($array[0]);
        $this->rawBody = trim($array[1]);

        $this->headers = array();

        $headerLines = preg_split('/\R/', $this->rawHeaders);
        $currentKey = '';
        foreach ($headerLines as $headerLine) {

            // New header
            if (preg_match('/^[a-zA-Z0-9\-_]+:\s/', $headerLine)) {
                list($key, $value) = explode(':', $headerLine);

                // Convert all keys to lower case
                $key = strtolower($key);

                $currentKey = $key;
                if (empty($this->headers[$key])) {
                    $this->headers[$key] = trim($value);
                } else {
                    $this->headers[$key] .= "\n" . trim($value);
                }
                // Multi-line header
            } else {
                $this->headers[$currentKey] .= "\n" . trim($headerLine);
            }
        }

        // Decode text
        if (strcasecmp($this->getHeader('Content-Transfer-Encoding'), 'quoted-printable' == 0)) {
            $this->rawBody = quoted_printable_decode($this->rawBody);
        }

        $contentType = $this->getHeader('Content-Type');
        // If content type is empty, assume it's text
        if (empty($contentType)) {
            $contentType = 'text/plain';
        }

        if (stripos($contentType, 'text/plain') !== false) {
            $this->bodyText = $this->rawBody;

        } elseif (stripos($contentType, 'multipart/') !== false) {

            // Grab the boudary token
            preg_match('/boundary="?([^"]+)"?/i', $contentType, $matches);

            if (!isset($matches[1])) {
                echo "\n\n";
                var_dump($this->headers);
                echo "\n";
                //echo $this->rawData;
                exit;
            }

            $boundary = $matches[1];

            // Split email into segments
            $emailSegments = preg_split("/--$boundary/", $this->rawBody, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($emailSegments as $key => &$emailSegment) {
                $emailSegment = trim($emailSegment);
                $emailSegment = trim($emailSegment, '-');
                if ($emailSegment == '') {
                    unset($emailSegments[$key]);
                }
            }

            // Looking for text segment
            foreach ($emailSegments as $segment) {

                if (stripos($segment, "Content-Type: text/plain") !== false) {
                    $this->bodyText = $segment;
                } elseif (stripos($segment, "Content-Type: text/html") !== false) {
                    $this->bodyHtml = $segment;
                } elseif (stripos($segment, "Content-Disposition: attachment") !== false) {
                    // Ignore atttachments
                } else {
                    //echo "UNKOWN SEGMENT\n";
                    //echo $segment;
                }
            }

        } elseif (stripos($contentType, 'text/html') !== false) {

            $this->bodyHtml = $this->rawBody;
            $this->bodyText = Html2Text\Html2Text::convert($this->rawBody);

        } else {

            var_dump($this->headers);

            echo $this->rawHeaders;
            echo $this->rawBody;

            echo "Problem!\n";
            //echo $this->rawData;

            exit;
        }
    }
}