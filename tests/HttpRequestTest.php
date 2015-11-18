<?php

use HelloFuture\SemanticProxy\Source\Value;
use HelloFuture\SemanticProxy\Source\HttpRequest;

require_once __DIR__ . '/../vendor/autoload.php';

class HttpRequestTest extends PHPUnit_Framework_TestCase {

	const JSON_DECODE  = 'HelloFuture\\SemanticProxy\\Transformer\\JsonDecode';
	const HTTP_REQUEST = 'HelloFuture\\SemanticProxy\\Source\\HttpRequest';

	public function testValidWebUrl() {

		$source = new HttpRequest(['url' => 'http://localhost:8000/test-1.txt']);
		$this->assertSame('Blizzard', $source->getData());
		$this->assertSame(200, $source->getMetaValue('statusCode'));

		$source = HttpRequest::create(['url' => 'http://localhost:8000/test-2.json.php'])->to(self::JSON_DECODE);
		$expected    = (object) ['fee' => 4078];
		$this->assertEquals($expected, $source->getData());

		$this->assertEquals('application/json; charset=utf-8', $source->getMetaValue('contentType'));
		$this->assertEquals('localhost:8000', $source->getMetaValue('responseHeaders')['Host']);
		$this->assertEquals('polar', $source->getMetaValue('responseHeaders')['X-Test-Custom']);
	}

	public function testShortParam() {

		$source = new HttpRequest('http://localhost:8000/test-1.txt');
		$this->assertSame('Blizzard', $source->getData());

	}

	public function testRequestHeaders() {
		$options = ['url' => 'http://localhost:8000/header-pass.php', 'headers' => ['X-Test-Custom' => 'arctic']];
		$source = HttpRequest::create($options)->to(self::JSON_DECODE);
		$this->assertEquals('arctic', $source->getData()->{'X-Test-Custom'});
	}

	/**
	 * @medium
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\CurlException
	 * @expectedExceptionCode 4
	 */
	public function testTimeout() {
		$options = ['url' => 'http://localhost:8000/timeout.php', 'timeout' => 1];
		HttpRequest::create($options)->getData();
	}

	/**
	 * @medium
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\CurlException
	 * @expectedExceptionCode 3
	 */
	public function testInvalidDomain() {
		HttpRequest::create(['url' => 'http://iceblizzard/'])->getData();
	}

}
