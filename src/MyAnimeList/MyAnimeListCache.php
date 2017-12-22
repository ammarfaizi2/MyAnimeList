<?php

namespace MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package MyAnimeList
 */
class MyAnimeListCache
{
	/**
	 * @var string
	 */
	private $hash;

	/**
	 * @var string
	 */
	private $mapFile;

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var array
	 */
	private $cacheMap = [];

	/**
	 * @var array
	 */
	private $relations = [];

	/**
	 * @var string
	 */
	private $relationsFile;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * Constructor.
	 *
	 * @param string $type
	 * @param string $title
	 */
	public function __construct($type, $title)
	{
		$this->type  = $type;
		$this->title = $title;
		$this->hash  = sha1($title);
		if (defined("MYANIMELIST_CACHE_DIR")) {
			$rootCacheDir   = MYANIMELIST_CACHE_DIR;
		} else {
			$rootCacheDir   = "/tmp/myanimelist";
		}
		$this->cacheDir 	 = $rootCacheDir."/".$type;
		$this->relationsFile = $this->cacheDir."/relations";
		$this->mapFile  	 = $this->cacheDir."/map";
		is_dir($rootCacheDir) or mkdir($rootCacheDir);
		is_dir($this->cacheDir) or mkdir($this->cacheDir);
		if (! is_dir($this->cacheDir)) {
			throw new \Exception("Cannot create directory ".$this->cacheDir, 1);
		}
		if (! is_writable($this->cacheDir)) {
			throw new \Exception($this->cacheDir. " is not writeable", 1);
		}
		if (! file_exists($this->mapFile)) {
			$this->cacheMap = [
				"name" 			=> $type,
				"last_updated"	=> time(),
				"data" 			=> []
			];
		} else {
			$this->cacheMap = json_decode(file_get_contents($this->mapFile), true);
			$this->cacheMap = is_array($this->cacheMap) ? $this->cacheMap : [
				"name" 			=> $type,
				"last_updated"	=> time(),
				"data" 			=> []
			];
		}
		if (! file_exists($this->relationsFile)) {
			$this->relations = [];
		} else {
			$this->relations = json_decode(file_get_contents($this->relationsFile), true);
			$this->relations = is_array($this->relations) ? $this->relations : [];
		}
	}

	/**
	 * @param array $data
	 */
	public function set($data)
	{
		if (is_array($data) && isset($data['entry'])) {
			if (isset($data['entry'][0])) {
				foreach ($data['entry'] as $key => $val) {
					isset($val['id']) and $this->relations[$this->hash][] = $val['id'];
				}
			} else {
				$this->relations[$this->hash][] = $data['entry']['id'];
			}
			$this->cacheMap['last_updated'] = time();
			$this->cacheMap['data'][$this->hash] = time();
			file_put_contents($this->relationsFile, json_encode($this->relations), LOCK_EX);
			file_put_contents($this->cacheDir."/".$this->hash, json_encode($data), LOCK_EX);
			file_put_contents($this->mapFile, json_encode($this->cacheMap), LOCK_EX);
		}
	}

	/**
	 * @return bool
	 */
	public function isCached()
	{
		return isset($this->cacheMap['data'][$this->hash]) and ($this->cacheMap['data'][$this->hash]+1209600) > time() and file_exists($this->cacheDir."/".$this->hash);
	}

	/**
	 * @return mixed
	 */
	public function get()
	{
		return json_decode(file_get_contents($this->cacheDir."/".$this->hash), true);
	}
}
