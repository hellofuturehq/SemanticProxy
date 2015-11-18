<?php

namespace HelloFuture\SemanticProxy\Source;

use HelloFuture\SemanticProxy\Exceptions\CurlException;

class HttpRequest extends AbstractSource {

	const DEFAULT_TIMEOUT = 120;

	public function __construct($options = []) {
		if (is_string($options)) {
			$options = ['url' => $options];
		}
		parent::__construct($options);
	}

	public function getData() {

		$url = $this->getOption('url');
		$ch  = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE,        false);
		curl_setopt($ch, CURLOPT_HEADER,         true);
		curl_setopt($ch, CURLOPT_TIMEOUT,        $this->getOption('timeout'));

		$headers = $this->getOption('headers');
		if ($headers && is_array($headers) and count($headers)) {
			$requestHeaders = [];
			foreach($headers as $index => $header) {
				$requestHeaders[] = $index. ': ' . $header;
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
		}

		$response = curl_exec($ch);

		if ($response === false) {
			$error = curl_error($ch);
			$errno = curl_errno($ch);
			$this->setMetaValue('curlError', $error);
			$this->setMetaValue('curlErrno', $errno);

			switch($errno) {
				case 3:
				case 6:
					$code = CurlException::PARSE_ERROR;
					break;
				case 28:
					$code = CurlException::TIMEOUT;
					break;
				default:
					$code = CurlException::UNKNOWN_ERROR;
			}

			throw new CurlException('curl error #' . $errno . ': ' . $error, $code);
		}

		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header     = substr($response, 0, $headerSize);
		$body       = substr($response, $headerSize);

		$this->setMetaValue('responseHeaders', $this->parseHeaders($header));
		$this->setMetaValue('contentType',     curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
		$this->setMetaValue('statusCode',      curl_getinfo($ch, CURLINFO_HTTP_CODE));

		return $body;

	}

	protected function parseHeaders($headerString) {
		$headers = array();
		foreach(explode("\n", $headerString) as $line) {
			$parts = explode(':', $line, 2);
			if (count($parts) == 2) {
				$headers[trim($parts[0])] = trim($parts[1]);
			}
		}
		return $headers;
	}

	public function getDefaultOptions() {
		return [
			'headers' => null,
			'timeout' => self::DEFAULT_TIMEOUT,
		];
	}

}
