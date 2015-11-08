<?php

include_once 'common.php';

// Получаем способ запуска
$php_sapi_name = php_sapi_name();

// Если скрипт запущен не из командной строки
if ($php_sapi_name != 'cli') {
	// Авторизация
	if (
			!isset($_POST['login']) ||
			!isset($_POST['pass']) ||
			$_POST['login'] != $settings['admin_login'] ||
			$_POST['pass'] != $settings['admin_password']
		) {
		print "<form action=\"./alidump_load.php\" method=\"post\">\n";
		print "<table cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n";
		print "<tr><td>Login:</td><td><input type=\"text\" name=\"login\"></td></tr>\n";
		print "<tr><td>Password:</td><td><input type=\"password\" name=\"pass\"></td></tr>\n";
		print "<tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Login\"</td></tr>\n";
		print "</table>\n";
		print "</form>\n";
		exit();
		
	}
}

// Инициализируем базу данных (создаём таблицы если они не существуют).
$DBAccess->InitDB();


// Начинаем считывать дамп
$xml = new XMLReader();
$xml->open('alidump.yml');


// Товары
$offers = array();
$pictures = array();

// Перебираем все тэги
while ($xml->read()) {
        // Если открывающий тэг категории
        if ($xml->name == 'category' && $xml->nodeType == XMLReader::ELEMENT) {
                        // Получаем идентификатор категории
                        $category_id = $xml->getAttribute('id');
                        // Добываем имя категории
                        $xml->read();
                        $category_title = $xml->value;
                        
                        // Добавляем категорию в БД
                        $DBAccess->CategoryAdd($category_id, $category_title);
                        print "Added/updated category &laquo;" . htmlspecialchars($category_title) . "&raquo;<br />\n";
			// Отправляем тест из буфера пользователю
			flush();
			ob_flush();
        }
        // Если открывающий тэг товара
        elseif ($xml->name == 'offer' && $xml->nodeType == XMLReader::ELEMENT) {
                        $offer = array();
                        // Получаем идентификатор товара
                        $offer['id'] = $xml->getAttribute('id');
                        // Вычитываем последующие тэги
                        while ($xml->read()) {
                                // Если открывающий тэг categoryId
                                if ($xml->name == 'categoryId' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем идентификатор категории
                                        $xml->read();
                                        $offer['id_category'] = $xml->value;
                                }
                                // Если открывающий тэг name
                                elseif ($xml->name == 'name' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем идентификатор категории
                                        $xml->read();
                                        $offer['name'] = $xml->value;
                                }
                                // Если открывающий тэг picture
                                elseif ($xml->name == 'picture' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем картинку
                                        $xml->read();
                                        $picture = $xml->value;
                                        // Если это первая картинка
                                        if (!isset($offer['picture'])) {
						// То она относится к товару
						$offer['picture'] = $picture;
                                        }
                                        // В любом случае добавляем картинку в массив
                                        $pictures[] = array(
							'id_offer' => $offer['id'],
							'picture' => $picture,
						);
                                }
                                // Если открывающий тэг price
                                elseif ($xml->name == 'price' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем идентификатор категории
                                        $xml->read();
                                        $offer['price'] = $xml->value;
                                }
                                // Если открывающий тэг currencyId
                                elseif ($xml->name == 'currencyId' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем идентификатор категории
                                        $xml->read();
                                        $offer['currency'] = $xml->value;
                                }
                                // Если открывающий тэг url
                                elseif ($xml->name == 'url' && $xml->nodeType == XMLReader::ELEMENT) {
                                        // Добываем идентификатор категории
                                        $xml->read();
                                        $offer['url'] = $xml->value;
                                }
                                // Если закрывающий тэг товара
                                elseif ($xml->name == 'offer' && $xml->nodeType == XMLReader::END_ELEMENT) {
					// Добавляем товар в массив
					$offers[] = $offer;
					// Если набрали достаточно товаров
					if (sizeof($offers) >= 500) {
						// Добавляем товары в базу
						$DBAccess->OfferAddMulti($offers);
						print "Added/updated " . sizeof($offers) . " offers<br />\n";
						$offers = array();
						// Добавляем картинки в базу
						$DBAccess->OfferPicturesAddMulti($pictures);
						print "Added/updated " . sizeof($pictures) . " pictures<br />\n";
						$pictures = array();
						
						
						// Отправляем тест из буфера пользователю
						flush();
						ob_flush();
						
					}
                                        //$DBAccess->OfferAdd($offer);
                                        break;
                                }
                        }
        }
}

// Если остались недобавленные товары
if (sizeof($offers)) {
	// Добавляем их
	$DBAccess->OfferAddMulti($offers);
	print "Added/updated " . sizeof($offers) . " offers<br />\n";
}
// Если остались недобавленные картинки
if (sizeof($pictures)) {
	$DBAccess->OfferPicturesAddMulti($pictures);
	print "Added/updated " . sizeof($pictures) . " pictures<br />\n";
}

// Обновляем количество товаров в категориях
$DBAccess->CategoryUpdateOffersCount();
print "Offers counter for categories updated<br />\n";

print "<br />\n[All done]<br />\n";
