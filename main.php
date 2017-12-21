<?php

require __DIR__ . "/vendor/autoload.php";

require __DIR__ . "/cred";

$out = MyAnimeList\MyAnimeList::animeSearch("sword art online");

var_dump($out);
