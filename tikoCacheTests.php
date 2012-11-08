<?php

	include("tikoCacheController.php");

	class cacheAPI implements TikoCacheAPI {

		private $item = null;
		private $cache;

		public function __construct($cache) {
			$this->cache = $cache;
		}

		public function getUrl() {
			return $_SERVER["SCRIPT_URI"];
		}

		public function get($key) {
			if (!isset($this->cache[$key])) {
				return null;
			}
			return $this->cache[$key];
		}

		public function set($key, $value) {
			$this->cache[$key] = $value;
		}

		public function resolve($item) {
			$this->item = $item;
		}

		public function getItem() {
			return $this->item;
		}

		public function remove($key) {
			unset($this->cache[$key]);
		}
	}

	/*
		Three basic scenarios
		We use a very simple memory-based cache to test the behaviour.
	*/

	$cache = new ArrayObject(); // To ensure passing by reference

	function test_firstRun($cache) {
		$cacheAPI = new cacheAPI($cache);
		$tiko = new tikoCacheController($cacheAPI);
		// Set required parameters
		$tiko->requireCacheParameter("user");
		$tiko->requireCacheParameter("time");
		// Set parameter values
		$tiko->fillCacheParameter("user", "Willem Mulder");
		$tiko->fillCacheParameter("time", "14th");
		// Set cache
		$result = $tiko->cache("<html><body>This is the text we want to be cached!</body></html>");
		if ($result === false) {
			fail("CACHING FAILED");
		} else {
			done("CACHING WORKED");
		}
	}
	test_firstRun($cache);

	function test_secondRun($cache) {
		$cacheAPI = new cacheAPI($cache);
		$tiko = new tikoCacheController($cacheAPI);
		// It should fetch requiredCacheParameters from cache
		// Set parameter values
		$tiko->fillCacheParameter("user", "Willem Mulder");
		$tiko->fillCacheParameter("time", "14th");
		// Since all required parameters are filled, the item should now be retrieved by cache. Let's check
		$result = $cacheAPI->getItem();
		if ($result !== "<html><body>This is the text we want to be cached!</body></html>") {
			fail("RETRIEVING FROM CACHE FAILED");
		} else {
			done("RETRIEVING FROM CACHE WORKED");
		}
	}
	test_secondRun($cache);

	function test_clearCache($cache) {
		$cacheAPI = new cacheAPI($cache);
		$tiko = new tikoCacheController($cacheAPI);
		$tiko->removeCacheForURL();
		// It should fetch requiredCacheParameters from cache
		// Set parameter values
		$tiko->fillCacheParameter("user", "Willem Mulder");
		$tiko->fillCacheParameter("time", "14th");
		// Since all required parameters are filled, the item should now be retrieved by cache. 
		// But, *since it is removed*, it should not be found. Let's check
		$result = $cacheAPI->getItem();
		if ($result !== null) {
			fail("CLEARING OF CACHE FOR URL FAILED");
		} else {
			done("CLEARING OF CACHE FOR URL WORKED");
		}
	}
	test_clearCache($cache);

	// =======

	function done($msg) {
		echo "<div style='background: #00ff00; color: #fff;'>" . $msg . "</div>";
	}

	function fail($msg) {
		echo "<div style='background: #ff0000; color: #fff;'>" . $msg . "</div>";
	}

?>