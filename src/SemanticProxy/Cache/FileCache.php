<?php

namespace HelloFuture\SemanticProxy\Cache;

class FileCache implements CacheInterface {

	protected $path;
	protected $timeToLive;

	public function __construct($path, $timeToLive) {
		if (substr($path, -1, 1) != '/') {
			$path .= '/';
		}
		$this->path       = $path;
		$this->timeToLive = $timeToLive;
	}

	static public function createFromOptions($options) {
		return new self($options['path'], $options['timeToLive']);
	}

	public function exists($key) {
		$path = $this->getPath($key);
		return file_exists($path);
	}

	public function valid($key, $timeToLive = null) {
		$path = $this->getPath($key);
		if (is_null($timeToLive)) {
			$timeToLive = $this->timeToLive;
		}
		return filemtime($path) + $timeToLive >= time();
	}

	public function write($key, $value) {
		$path = $this->getPath($key);
		file_put_contents($path, $this->encode($value));
	}

	public function read($key) {
		$path = $this->getPath($key);
		return $this->decode(file_get_contents($path));

	}

	public function drop($key) {
		$path = $this->getPath($key);
		unlink($path);
	}

	public function dropAll() {
		$pattern = $this->getPath('*');
		foreach(glob($pattern) as $path) {
			unlink($path);
		}
	}

	public function getPath($key) {
		return $this->path . $key . '.cache';
	}

	// Ltodo: part of interface? Or even own object?
	public function encode($value) {
		return serialize($value);
	}

	public function decode($value) {
		return unserialize($value);
	}

}
