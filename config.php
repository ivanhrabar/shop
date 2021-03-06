<?php
// Настройки
$settings = array(
	// Ключ для доступа к API
	'user_api_key' => 'bf09ee66cd2b54f9d9ccc51ee750cac6',
	// Хэш для построения диплинков
	'user_deep_link_hash' => 'rjaln4eks8k8g1fmgeofwn4qvn875465',
	
	// Количество товаров на странице
	'items_per_page' => 15,
	
	// Количество товаров в RSS-ленте
	'rss_items_count' => 15,
	
	// Минимальная цена товара
	'price_min' => 0.0,
	// Максимальная цена товара
	'price_max' => 1000000.0,
	
	// Название нашего сайта
	'site_name' => 'Universe of goods',
	
	// Язык описаний товаров (может быть en или ru)
	'lang' => 'ru',
	
	// Желаемая валюта
	// Поддерживаются как минимум USD, EUR, RUR, UAH, KZT. Подробнее - в документации
	'currency' => 'USD',
	
	// Используемая библиотека кэширования
	// Если есть поддержка на хостинге то крайне рекоммендуется включить
	// Возможные значения: none, apc, xcache, memcache, memcached, wincache
	'cache_library' => 'none',
	
	// Только если в качестве кэша выбран memcache или memcached
	'memcached_host' => '127.0.0.1',
	'memcached_port' => 11211,
	'memcached_pconnect' => TRUE,
	
	// Настройки MySQL. Используются если в качестве кэша указан mysql
	'mysql_host' => '127.0.0.1',
	'mysql_user' => 'adminfgjhnTi',
	'mysql_pass' => 'qi5JeKVVvJjK',
	'mysql_base' => 'shop',
	'mysql_persist' => FALSE,
);

