<?php
// Настройки
include_once dirname(__FILE__) . '/config.php';

// Базовые функции
include_once dirname(__FILE__) . '/libs/clCommon.php';

$Common = new clCommon();

// Цепляем библиотеку для работы с БД
include_once dirname(__FILE__) . '/libs/clDBAccess.php';

// Соединяемся с БД
$DBAccess = new clDBAccess(
                $settings['dbhost'],
                $settings['dbuser'],
                $settings['dbpass'],
                $settings['dbname'],
                $settings['db_pconnect']
        );


// Цепляем библиотеку для работы с путями
include_once dirname(__FILE__) . '/libs/clPath.php';

// Создаём объект для работы с путями
$Path = new clPath(
		$settings['deep_link_hash']
	);
	
// Библиотека для работы с языками
include_once dirname(__FILE__) . '/libs/clLang.php';

// Создаём объект для работы с языками
$Lang = new clLang(
		$settings['lang']
	);

// Библиотека для работы с курсами валют ЦБ РФ
include_once dirname(__FILE__) . '/libs/clCBRF.php';

// Создаём объект
$CBRF = new clCBRF($DBAccess->CurrencyGetList());

// Если необходимо обновить данные
if ($CBRF->NeedUpdate()) {
	// Пытаемся получить информацию от ЦБ РФ
	$CBRF->UpdateRates();
	// Если теперь данные актуальные
	if (!$CBRF->NeedUpdate()) {
		// Сохраняем их в БД
		foreach ($CBRF->currency_hash as $key => $value) {
			$DBAccess->CurrencyUpdateRate($key, $value['rate'], $value['description']);
		}
	}
}




