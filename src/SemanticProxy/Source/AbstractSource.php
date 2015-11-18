<?php

namespace HelloFuture\SemanticProxy\Source;

use HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException;
use HelloFuture\SemanticProxy\OutputInterface;

abstract class AbstractSource implements OutputInterface {

	private $options = null;
	private $meta    = [];

	public function __construct($options = []) {
		$this->options = array_merge($this->getDefaultOptions(), $options);
		if (!$this->validateOptions()) {
			throw new InvalidOptionsException('invalid call of ' . get_class($this));
		}
	}

	static public function create($input, $options = []) {
		return new static($input, $options);
	}

	public function to($className, $options = []) {
		return new $className($this, $options);
	}

	abstract public function getData();

	public function getScent() {
		return [
			(object) [
				'class'   => get_class($this),
				'options' => $this->getOptions()
			]
		];
	}

	final public function getOptions() {
		return $this->options;
	}

	final public function getOption($key, $default = null) {
		$options = $this->getOptions();
		return isset($options[$key]) ? $options[$key] : $default;
	}

	final public function getMetaValue($key, $default = null) {
		if (isset($this->meta[$key])) {
			return $this->meta[$key];
		} else {
			$inner = $this->getInner();
			if ($inner) {
				return $inner->getMetaValue($key, $default);
			} else {
				return $default;
			}
		}
	}

	final public function setMetaValue($key, $value) {
		$this->meta[$key] = $value;
	}


	public function getDefaultOptions() {
		return [];
	}

	public function validateOptions() {
		foreach($this->optionRules() as $option => $pattern) {
			$value = $this->getOption($option);
			if (is_null($value) || !preg_match($pattern, $value)) {
				return false;
			}
		}
		return true;
	}

	public function optionRules() {
		return [];
	}

}
