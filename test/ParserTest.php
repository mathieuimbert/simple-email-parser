<?php
class ParserTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testLoadFile()
    {
        // Arrange
        $a = new \MathieuImbert\SimpleEmailParser\Parser();
        $a->loadFile(__DIR__ . '/multipart_email.eml');

        $this->assertStringEqualsFile(__DIR__ . '/multipart_email.eml', $a->rawData);
    }
}