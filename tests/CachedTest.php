<?php

use HelloFuture\SemanticProxy\Cache\FileCache;
use HelloFuture\SemanticProxy\Transformer\AbstractTransformer;
use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class CachedTest extends PHPUnit_Framework_TestCase {

	const CACHED = 'HelloFuture\\SemanticProxy\\Transformer\\Cached';

	protected function tearDown() {
		Randomize::resetCounter();

		$options = $this->getCacheOptions();
		$cache   = FileCache::createFromOptions($options);
		$cache->dropAll();
	}

	public function testInOut() {
		$options     = $this->getCacheOptions();
		$transformer = Value::create('treeline')->to(self::CACHED, $options);
		$this->assertSame('treeline', $transformer->getData());
	}

	public function testCached() {
		$options      = $this->getCacheOptions();

		$transformer1 = Value::create('')->to('Randomize')->to(self::CACHED, $options);
		$data         = $transformer1->getData();
		$transformer2 = Value::create('')->to('Randomize')->to(self::CACHED, $options);

		$this->assertSame($data, $transformer2->getData());
		$this->assertSame(1, Randomize::getCounter());
	}

	public function testExpired() {
		$options      = $this->getCacheOptions();

		$transformer1 = Value::create('')->to('Randomize')->to(self::CACHED, $options);
		$data         = $transformer1->getData();
		$this->assertSame($data, $transformer1->getData()); // Randomize::$counter++ since first read

		// make the cache file outdated
		$key  = $transformer1->getMetaValue('cacheKey');
		$path = $transformer1->getMetaValue('cache')->getPath($key);
		touch($path, time() - 1000000);
		clearstatcache();

		$transformer2 = Value::create('')->to('Randomize')->to(self::CACHED, $options);
		$this->assertNotEquals($data, $transformer1->getData()); // Randomize::$counter++ since outdated
		$this->assertNotEquals($data, $transformer2->getData()); // Randomize::$counter++ since first read
		$this->assertSame(3, Randomize::getCounter());


	}

	public function testIndependance() {
		$options      = $this->getCacheOptions();

		$transformer1 = Value::create('arctic fox')->to(self::CACHED, $options);
		$this->assertSame('arctic fox', $transformer1->getData());

		$transformer2 = Value::create('wolverine')->to(self::CACHED, $options);
		$this->assertSame('wolverine', $transformer2->getData());
		$this->assertSame('arctic fox', $transformer1->getData());
	}

	/**
	 *	@expectedException HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException
	 */
	public function testNoPathOption() {
		$transformer1 = Value::create('polar bear')->to(self::CACHED, []);
	}

	protected function getCacheOptions() {
		return [
			'path'                => __DIR__ . '/cache/',
			'timeToLive'          => 3600,
		];
	}

	// test write error (when not in loose mode)
	// test read error  (when not in loose mode)

}

class Randomize extends AbstractTransformer {

	static protected $counter = 0;

	static public function resetCounter() {
		self::$counter = 0;
	}

	static public function getCounter() {
		return self::$counter;
	}

	protected function transform($input) {
		self::$counter++;
		return rand();
	}

}
