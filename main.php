<?php

require __DIR__ . "/vendor/autoload.php";

require __DIR__ . "/cred";

//$out = MyAnimeList\MyAnimeList::animeSearch("sword art online");
//$out = MyAnimeList\MyAnimeList::mangaSearch("one piece");
$out = MyAnimeList\MyAnimeList::animeInfo('31765');

var_dump($out);
