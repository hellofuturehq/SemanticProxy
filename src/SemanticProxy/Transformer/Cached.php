<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Exceptions\EmptyException;
use HelloFuture\SemanticProxy\Exceptions\JsonException;

class Cached extends AbstractTransformer {

	public function __construct($input, $options = array()) {
		parent::__construct($input, $options);
		$this->setMetaValue('cache',    $this->getCacheInstance());
		$this->setMetaValue('cacheKey', $this->getCacheKey());
	}

	protected function getCacheInstance() {
		$cacheClass = $this->getOption('cacheClass');
		return call_user_func(array($cacheClass, 'createFromOptions'), $this->getOptions());
	}

	protected function getCacheKey() {
		return md5(json_encode($this->getScent()));
	}

	public function getInnerData() {
		$dataExists = false;
		$cache      = $this->getMetaValue('cache');
		$key        = $this->getMetaValue('cacheKey');

		// try to get from cache
		if ($cache->exists($key)) {
			if ($cache->valid($key)) {
				$inputData  = $cache->read($key);
				$dataExists = true;
			}
		}
		// read from inner transformer (default behaviour)
		if (!$dataExists) {
			$inputData = parent::getInnerData();
			$cache->write($key, $inputData);
		}
		return $inputData;
	}

	protected function transform($input) {
		return $input;
	}

	public function getDefaultOptions() {
		return [
			'cacheClass' => 'HelloFuture\\SemanticProxy\\Cache\\FileCache',
			'timeToLive' => 300, // 5 minutes
		];
	}

	public function optionRules() {
		return [
			'path' => '/./',
		];
	}

}
