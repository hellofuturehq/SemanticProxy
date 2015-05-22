<?php

namespace HelloFuture\SemanticProxy\Cache;

interface CacheInterface {

	public function exists($key);

	public function valid($key, $timeToLive = null);

	public function write($key, $value);

	public function read($key);

	public function drop($key);

	public function dropAll();

}
