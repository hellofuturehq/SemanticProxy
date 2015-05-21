<?php

namespace HelloFuture\SemanticProxy\Transformer;

use DOMDocument;
use HelloFuture\SemanticProxy\Exceptions\EmptyException;
use HelloFuture\SemanticProxy\Exceptions\DomException;

class XmlToDom extends AbstractTransformer {

	protected function transform($input) {
		if (is_null($input) || $input === '') {
			throw new EmptyException;
		}

		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$doc = $this->loadIntoDoc($input);
		foreach(libxml_get_errors() as $error) {
			if ($error->level != LIBXML_ERR_WARNING) {
				throw new DomException('dom error #' . $error->code, DomException::PARSE_ERROR);
			}
		}
		libxml_clear_errors();
		return $doc;
	}

	protected function loadIntoDoc($input) {
		$doc = new DOMDocument();
		$doc->loadXML($input);
		return $doc;
	}

}
