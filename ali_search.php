<?php

include_once 'common.php';

// Получаем список категорий
$categories_list = $DBAccess->CategoryGetAll();
// Здесь будет хэш id => info
$categories_hash = array();
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = FALSE;
	$categories_hash[$value['id']] = $categories_list[$key];
}

// Получаем поисковый запрос
$search_query = isset($_GET['q']) ? $_GET['q'] : '';

// Выполняем поиск
$offers = $DBAccess->OffersSearchForQuery($search_query, $settings['items_per_page']);

// Дополняем информацию о товарах
foreach ($offers as $key => $value) {
	// Информация о категории
	$offers[$key]['category'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['title'] : '';
	$offers[$key]['category_link'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['link'] : '';
	// "Прямая" ссылка
	$offers[$key]['url'] = $Path->Go($value['id']);
	// Ссылка на более подробную информацию
	$offers[$key]['link'] = $Path->Offer($value['id'], $value['name']);
	// Конвертируем цену
	$price_tmp = $CBRF->Convert($value['price'], $value['currency'], $settings['currency']);
	$offers[$key]['price'] = $price_tmp['sum'];
	$offers[$key]['currency'] = $price_tmp['currency'];
}


// Цепляем шаблон
include_once $Common->GetTemplatePath('search');
