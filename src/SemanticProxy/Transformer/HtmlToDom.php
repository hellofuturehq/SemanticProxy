<?php

namespace HelloFuture\SemanticProxy\Transformer;

use DOMDocument;
use HelloFuture\SemanticProxy\Exceptions\EmptyException;
use HelloFuture\SemanticProxy\Exceptions\DomException;

class HtmlToDom extends XmlToDom {

	protected function loadIntoDoc($input) {
		$doc      = new DOMDocument;

		$encoding = $this->getOption('encoding');

		if ($encoding) {
			$input = '<?xml version="1.0" encoding="' . $encoding . '"?' . '>' . $input;
		}

		$doc->loadHTML($input);

		if ($encoding) {
			$doc->encoding = $encoding;
		}

		return $doc;
	}

	public function getDefaultOptions() {
		$defaultOptions = [
			'encoding' => 'UTF-8'
		];
		return array_merge(parent::getDefaultOptions(), $defaultOptions);
	}

}
