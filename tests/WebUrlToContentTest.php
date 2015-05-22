<?php

use HelloFuture\SemanticProxy\Transformer\WebUrlToContent;
use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class WebUrlToContentTest extends PHPUnit_Framework_TestCase {

	const JSON_DECODE = 'HelloFuture\\SemanticProxy\\Transformer\\JsonDecode';
	const WEB_URL_TO_CONTENT = 'HelloFuture\\SemanticProxy\\Transformer\\WebUrlToContent';

	public function testValidWebUrl() {

		$transformer = Value::create('http://localhost:8000/test-1.txt')->to(self::WEB_URL_TO_CONTENT);
		$this->assertSame('Blizzard', $transformer->getData());
		$this->assertSame(200, $transformer->getMetaValue('statusCode'));

		$transformer = Value::create('http://localhost:8000/test-2.json.php')->to(self::WEB_URL_TO_CONTENT)->to(self::JSON_DECODE);
		$expected    = (object) ['fee' => 4078];
		$this->assertEquals($expected, $transformer->getData());

		$this->assertEquals('application/json; charset=utf-8', $transformer->getMetaValue('contentType'));
		$this->assertEquals('localhost:8000', $transformer->getMetaValue('responseHeaders')['Host']);
		$this->assertEquals('polar', $transformer->getMetaValue('responseHeaders')['X-Test-Custom']);
	}

	public function testRequestHeaders() {
		$transformer = Value::create('http://localhost:8000/header-pass.php')
			->to(self::WEB_URL_TO_CONTENT, ['headers' => ['X-Test-Custom' => 'arctic']])
			->to(self::JSON_DECODE)
		;
		$this->assertEquals('arctic', $transformer->getData()->{'X-Test-Custom'});
	}

	/**
	 * @medium
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\CurlException
	 * @expectedExceptionCode 4
	 */
	public function testTimeout() {
		Value::create('http://localhost:8000/timeout.php')->to(self::WEB_URL_TO_CONTENT, ['timeout' => 1])->getData();
	}

	/**
	 * @medium
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\CurlException
	 * @expectedExceptionCode 3
	 */
	public function testInvalidDomain() {
		Value::create('http://iceblizzard/')->to(self::WEB_URL_TO_CONTENT)->getData();
	}

}
