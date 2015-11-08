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


// Получаем идентификатор
$id = isset($_GET['id']) ? $Common->IntValue($_GET['id']) : 0;

// Пытаемся получить информацию о товаре
$offer_info = $DBAccess->OfferGetByID($id);

// Если информация о товаре была получена
if ($offer_info) {
	// Дополняем данные
	// "Прямая" ссылка
	$offer_info['url'] = $Path->Go($offer_info['id']);
	// Информация о категории
	$offer_info['category'] = isset($categories_hash[$offer_info['id_category']]) ? $categories_hash[$offer_info['id_category']]['title'] : '';
	$offer_info['category_link'] = isset($categories_hash[$offer_info['id_category']]) ? $categories_hash[$offer_info['id_category']]['link'] : '';
	
	// Получаем картинки товара
	$offer_info['images'] = $DBAccess->OfferPicturesGetForOffer($offer_info['id']);
	// Если в массиве нет основной картинки (хак для сайтов, мигрирующих со старой версии CMS)
	if ($offer_info['picture'] != '' && !in_array($offer_info['picture'], $offer_info['images']) ) {
		$offer_info['images'][] = $offer_info['picture'];
	}
	// Конвертируем цену
	$price_tmp = $CBRF->Convert($offer_info['price'], $offer_info['currency'], $settings['currency']);
	$offer_info['price'] = $price_tmp['sum'];
	$offer_info['currency'] = $price_tmp['currency'];
	
	// Получаем случайные товары
	$offers = $DBAccess->OffersGetRandom(3);

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

}



// Цепляем шаблон
include_once $Common->GetTemplatePath('good');
