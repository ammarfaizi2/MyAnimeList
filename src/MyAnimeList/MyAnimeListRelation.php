<?php

namespace MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.2
 * @package MyAnimeList
 */
class MyAnimeListRelation
{	

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var array
	 */
	private $relations = [];

	/**
	 * @var string
	 */
	private $relationFile;

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @param string $type
	 * @param int	 $id
	 */
	public function __construct($type, $id)
	{
		$this->type = $type;
		$this->id   = $id;
		if (defined("MYANIMELIST_CACHE_DIR")) {
			$rootCacheDir   = MYANIMELIST_CACHE_DIR;
		} else {
			$rootCacheDir   = "/tmp/myanimelist";
		}
		$this->cacheDir		= $rootCacheDir."/".$type;
		$this->relationFile = $this->cacheDir."/relations";
		if (file_exists($this->relationFile)) {
			$this->relations = json_decode(file_get_contents($this->relationFile), true);
			$this->relations = is_array($this->relations) ? $this->relations : [];
		}
	}

	/**
	 * @return array
	 */
	public function get()
	{
		foreach ($this->relations as $key => $val) {
			if ($search = array_search($this->id, $val)) {
				$location = $key;
				break;
			}
		}
		if (! isset($location)) {
			return false;
		} else {
			$data = json_decode(file_get_contents($this->cacheDir."/".$location), true);
			if (is_array($data) && isset($data['entry'])) {
				if (isset($data['entry'][0])) {
					foreach ($data['entry'] as $key => $val) {
						if ($val['id'] === $this->id) {
							return $data['entry'][$key];
						}
					}
				} else {
					return $data['entry'];
				}
			} else {
				return false;
			}
		}
	}
}