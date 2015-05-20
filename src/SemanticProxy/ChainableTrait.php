<?php

namespace HelloFuture\SemanticProxy;

trait ChainableTrait {

	public function to($className, $options = array()) {
		return new $className($this, $options);
	}

}
