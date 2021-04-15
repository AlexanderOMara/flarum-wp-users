<?php

namespace AlexanderOMara\FlarumWPUsers\Compat;

use Flarum\Extend\SimpleFlarumSearch as Base;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

/**
 * SimpleFlarumSearch compat for Flarum 16.
 *
 * https://github.com/flarum/core/issues/2712
 */
class SimpleFlarumSearch extends Base {
	private $__fullTextGambit;
	private $__searcher;

	public function __construct($searcherClass) {
		parent::__construct($searcherClass);
		$this->__searcher = $searcherClass;
	}

	public function setFullTextGambit($gambitClass) {
		$this->__fullTextGambit = $gambitClass;
		return parent::setFullTextGambit($gambitClass);
	}

	public function extend(Container $container, Extension $extension = null) {
		if (!is_null($this->__fullTextGambit)) {
			// Call extend, parent calls resolving instead.
			$container->extend(
				'flarum.simple_search.fulltext_gambits',
				function($bits) {
					$bits[$this->__searcher] = $this->__fullTextGambit;
					return $bits;
				}
			);
		}
		return parent::extend($container, $extension);
	}
}
