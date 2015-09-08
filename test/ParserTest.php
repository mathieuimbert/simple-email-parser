<?php

class ParserTest extends PHPUnit_Framework_TestCase
{

    public function testLoadFile()
    {
        $a = new \MathieuImbert\SimpleEmailParser\Parser();
        $a->loadFile(__DIR__ . '/multipart_email.eml');

        $this->assertStringEqualsFile(__DIR__ . '/multipart_email.eml', $a->rawData);
    }

    public function testLoadContent()
    {
        $a = new \MathieuImbert\SimpleEmailParser\Parser();

        $content = file_get_contents(__DIR__ . '/multipart_email.eml');
        $a->loadContent($content);

        $this->assertStringEqualsFile(__DIR__ . '/multipart_email.eml', $a->rawData);
    }

    public function testParse()
    {
        $a = new \MathieuImbert\SimpleEmailParser\Parser();
        $a->loadFile(__DIR__ . '/multipart_email.eml');

        $a->parse();

        $headers = $a->getHeaders();

        $this->assertCount(22, $headers);
        $this->assertEquals($a->getHeader('from'), 'Mathieu Imbert <mimbert@nexweb.ca>');
    }
}