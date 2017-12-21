<?php

namespace MyAnimeList;

if (! defined("MYANIMELIST_USER")) {
	throw new \Exception("MYANIMELIST_USER is not defined!", 1);
}

if (! defined("MYANIMELIST_PASS")) {
	throw new \Exception("MYANIMELIST_PASS is not defined!", 1);
}

use Curl\Curl;
use MyAnimeList\MyAnimeListCache;
use MyAnimeList\MyAnimeListRelation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package MyAnimeList
 */
class MyAnimeList
{
	public static function animeSearch($title)
	{
		$cache = new MyAnimeListCache('anime', $title);
		if ($cache->isCached()) {
			return $cache->get();
		} else {
			$cache->set($result = self::search('anime', $title));
			return $result;
		}
	}

	public static function mangaSearch($title)
	{
		$cache = new MyAnimeListCache('manga', $title);
		if ($cache->isCached()) {
			return $cache->get();
		} else {
			$cache->set($result = self::search('manga', $title));
			return $result;
		}
	}

	public static function mangaInfo($id)
	{
		$relation = new MyAnimeListRelation('manga', $id);
		return $relation->get();
	}

	public static function animeInfo($id)
	{
		$relation = new MyAnimeListRelation('anime', $id);
		return $relation->get();
	}

	private static function search($type, $title)
	{
		if ($type === 'anime') {
			$ch = new Curl("https://myanimelist.net/api/anime/search.xml?q=".urlencode($title));
		} elseif ($type === 'manga') {
			$ch = new Curl("https://myanimelist.net/api/manga/search.xml?q=".urlencode($title));
		}
		$ch->setOpt(
			[
				CURLOPT_USERPWD => MYANIMELIST_USER.":".MYANIMELIST_PASS
			]
		);
		$out = $ch->exec();
		if ($errno = $ch->errno()) {
			throw new \Exception("Error ({$errno}): ".$ch->error(), 1);
		}
		var_dump($out);
		return json_decode(json_encode(simplexml_load_string($out)), true);
	}
}
