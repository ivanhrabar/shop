<?php

include_once 'common.php';

// Получаем идентификатор категории и номер страницы
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page < 1) $page = 1;

// Получаем список категорий
$categories_list = $DBAccess->CategoryGetAll();
// Здесь будет хэш id => info
$categories_hash = array();
// Здесь будет информация о текущей категории
$current_category = FALSE;
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = ($value['id'] == $id);
	$categories_hash[$value['id']] = $categories_list[$key];
	if ($value['id'] == $id) {
		$current_category = $categories_list[$key];
	}
}

// Получаем товары
$offers = $DBAccess->OffersGetForCategory($id, ($page-1)*$settings['items_per_page'], $settings['items_per_page']);
// Получаем общее количество товаров для выбранной категории
$total_offers = $DBAccess->SelectFoundRows();

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

// Строим пейджер
$pages = array();
// Общее число страниц
$page_count = ceil($total_offers / $settings['items_per_page']);
// А нужен ли вообще пейджер?
if ($current_category && $page_count > 1) {
	$page_min = $page - 2;
	if ($page_min < 1) $page_min = 1;
	
	$page_max = $page + 2;
	if ($page_max > $page_count) $page_max = $page_count;
	
	if ($page_min > 1) {
		$pages[] = array(
				'page' => '<<',
				'link' => $Path->Category($id, $current_category['title'], 1),
			);
	}
	
	if ($page > 1) {
		$pages[] = array(
				'page' => '<',
				'link' => $Path->Category($id, $current_category['title'], $page - 1),
			);
	}
	
	for ($i = $page_min; $i <= $page_max; $i++) {
		$pages[] = array(
				'page' => $i,
				'link' => $i == $page ? '' : $Path->Category($id, $current_category['title'], $i),
			);
	}

	if ($page < $page_count) {
		$pages[] = array(
				'page' => '>',
				'link' => $Path->Category($id, $current_category['title'], $page + 1),
			);
	}
	
	if ($page_max < $page_count) {
		$pages[] = array(
				'page' => '>>',
				'link' => $Path->Category($id, $current_category['title'], $page_count),
			);
	}
}

// Цепляем шаблон
include_once $Common->GetTemplatePath('category');

