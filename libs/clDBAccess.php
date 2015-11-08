<?php

class clDBAccess extends mysqli {
	// Имена таблиц
	const TABLE_NAME_CATEGORIES     = 'ali_data_categories';
	const TABLE_NAME_OFFERS         = 'ali_data_offers';
	const TABLE_NAME_OFFER_PICTURES = 'ali_data_offer_pictures';
	const TABLE_NAME_CURRENCY_RATE  = 'ali_data_currency_rate';
	
	//======================================================================
	// Конструктор
	public function __construct($dbhost, $dbuser, $dbpass, $dbname, $pconnect = FALSE) {
		// Создаём объект
		parent::__construct(
                        ($pconnect ? 'p:' : '') . $dbhost,
                        $dbuser,
                        $dbpass,
                        $dbname
                );
                // Если случилась ошибка
		if ($this->connect_error) {
			print 'Connect Error: ' . $this->connect_error . "\n";
			exit();
		}
		// Указываем кодировку
                $this->set_charset('utf8');
	}
	//======================================================================
	
	//======================================================================
	// Инициализация базы данные
	public function InitDB() {
		$this->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_CATEGORIES . '` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`title` varchar(255) NOT NULL DEFAULT \'\',
					`count` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				KEY `count` (`count`)
			) ENGINE=InnoDB CHARSET=utf8');
		$this->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_OFFERS . '` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`name` varchar(2048) NOT NULL DEFAULT \'\',
					`description` varchar(4096) NOT NULL DEFAULT \'\',
					`id_category` bigint(20) NOT NULL DEFAULT 0,
					`price` decimal(10,2) NOT NULL DEFAULT 0,
					`currency` varchar(3) NOT NULL DEFAULT \'\',
					`picture` varchar(2048) NOT NULL DEFAULT \'\',
					`url` varchar(2048) NOT NULL DEFAULT \'\',
					`rand` int(11) NOT NULL DEFAULT 0,
					`added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				KEY `id_category` (`id_category`),
				KEY `id_category+added_at` (`id_category`, `added_at`),
				KEY `rand` (`rand`)
			) ENGINE=InnoDB CHARSET=utf8');
		$this->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_OFFER_PICTURES . '` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`id_offer` bigint(20) NOT NULL DEFAULT 0,
					`disabled` int(11) NOT NULL DEFAULT 0,
					`picture` varchar(2048) NOT NULL DEFAULT \'\',
					`picture_crc32` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id_offer+picture_crc32` (`id_offer`, `picture_crc32`),
				KEY `id_offer+disabled` (`id_offer`, `disabled`)
			) ENGINE=InnoDB CHARSET=utf8');
		$this->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_CURRENCY_RATE . '` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`currency` varchar(3) NOT NULL DEFAULT \'\',
					`rate` float NOT NULL DEFAULT 0,
					`description` varchar(255) NOT NULL DEFAULT \'\',
					`last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `currency` (`currency`)
			) ENGINE=InnoDB CHARSET=utf8');
		$this->query('INSERT INTO ' . self::TABLE_NAME_CURRENCY_RATE . ' SET
					`currency` = \'RUR\',
					`rate` = 1,
					`description` = \'Russian Ruble\',
					`last_update` = \'0000-00-00 00:00:00\'
				ON DUPLICATE KEY UPDATE
					`rate` = 1,
					`description` = \'Russian Ruble\',
					`last_update` = \'0000-00-00 00:00:00\'');
	}
	//======================================================================

	//======================================================================
        // Функция для нормализации чисел
        // Написана в связи с проблемами у intval на 32-битных системах
	private function IntValue($val) {
		$val = preg_replace('{[^\d]+}ui', '', $val);
		if ($val == '') $val = 0;
		return $val;
	}
        //======================================================================
        
	//======================================================================
	// Обновлениче числа товаров в каждой категории
	public function CategoryUpdateOffersCount() {
		$this->query("UPDATE `" . self::TABLE_NAME_CATEGORIES . "` SET
					`count` = (
							SELECT COUNT(*)
							FROM `" . self::TABLE_NAME_OFFERS . "`
							WHERE `id_category` = `" . self::TABLE_NAME_CATEGORIES . "`.`id`
						)");
	}
	//======================================================================
	
	//======================================================================
	// Обёртка для SELECT FOUND_ROWS()
	public function SelectFoundRows() {
		// Возвращаемый результат
		$rv = 0;
		// Пытаемся получить данные
		if ($result = $this->query("SELECT FOUND_ROWS() AS `rows`")) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				$rv_tmp = $result->fetch_assoc();
				$rv = $rv_tmp['rows'];
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Добавление категории
	public function CategoryAdd($id, $title) {
		// Делаем данные безопасными
		$id = $this->IntValue($id);
		$title = $this->real_escape_string($title);
		// Выполняем запрос
		$rv = $this->query("INSERT INTO `" . self::TABLE_NAME_CATEGORIES . "` SET `id` = $id, `title` = '$title' ON DUPLICATE KEY UPDATE `title` = '$title'");
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Получение списка категорий
	public function CategoryGetAll() {
		$rv = array();
		// Пытаемся получить данные
		if ($result = $this->query("SELECT * FROM `" . self::TABLE_NAME_CATEGORIES . "` WHERE `count` > 0")) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				while ($row = $result->fetch_assoc()) {
					$rv[] = $row;
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Добавление пачки товаров
	public function OfferAddMulti($offers) {
		// Если какая-то хрень
		if (!is_array($offers)) {
			// Значит и делать ничего не надо
			return FALSE;
		}
		// Перебираем товары
		$offer_values = array();
		foreach ($offers as $offer) {
			// Если какая-то хрень
			if (!is_array($offer)) {
				// Значит и делать ничего не надо
				return FALSE;
			}
			// Препарируем входные данные
			$id = isset($offer['id']) ? $this->IntValue($offer['id']) : '';
			$name = isset($offer['id']) ? $this->real_escape_string($offer['name']) : '';
			$id_category = isset($offer['id']) ? $this->IntValue($offer['id_category']) : '';
			$price = isset($offer['id']) ? floatval($offer['price']) : '';
			$currency = isset($offer['id']) ? $this->real_escape_string($offer['currency']) : '';
			$picture = isset($offer['id']) ? $this->real_escape_string($offer['picture']) : '';
			$url = isset($offer['id']) ? $this->real_escape_string($offer['url']) : '';
			$rand = rand(0, 999);
			// Строим строку для запроса
			$offer_values[] = "($id, '$name', $id_category, $price, '$currency', '$picture', '$url', $rand)";
		}
		// Строим запрос
		$sql_query = "INSERT INTO `" . self::TABLE_NAME_OFFERS . "`
						(`id`, `name`, `id_category`, `price`, `currency`, `picture`, `url`, `rand`)
					VALUES " . implode(', ', $offer_values) . "
					ON DUPLICATE KEY UPDATE
						`id_category` = VALUES(`id_category`),
						`price` = VALUES(`price`),
						`currency` = VALUES(`currency`),
						`picture` = VALUES(`picture`),
						`url` = VALUES(`url`),
						`rand` = VALUES(`rand`)";
		// Выполняем запрос
		$rv = $this->query($sql_query);
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Добавление пачки картинок
	public function OfferPicturesAddMulti($pictures) {
		// Если какая-то хрень
		if (!is_array($pictures)) {
			// Значит и делать ничего не надо
			return FALSE;
		}
		// Перебираем товары
		$picture_values = array();
		foreach ($pictures as $picture_data) {
			// Если какая-то хрень
			if (!is_array($picture_data)) {
				// Значит и делать ничего не надо
				return FALSE;
			}
			// Препарируем входные данные
			$id_offer = isset($picture_data['id_offer']) ? $this->IntValue($picture_data['id_offer']) : '';
			$picture = isset($picture_data['picture']) ? $this->real_escape_string($picture_data['picture']) : '';
			$picture_crc32 = crc32($picture);
			// Строим строку для запроса
			$picture_values[] = "($id_offer, '$picture', $picture_crc32)";
		}
		// Строим запрос
		$sql_query = "INSERT INTO `" . self::TABLE_NAME_OFFER_PICTURES . "`
						(`id_offer`, `picture`, `picture_crc32`)
					VALUES " . implode(', ', $picture_values) . "
					ON DUPLICATE KEY UPDATE
						`disabled` = 0";
		// Выполняем запрос
		$rv = $this->query($sql_query);
		// Возвращаем результат
		return $rv;
	}
	//======================================================================

	//======================================================================
	// Получение информации о товаре
	public function OfferGetByID($id) {
		// Препарируем входные данные
		$id = $this->IntValue($id);
		// Возвращаемый результат
		$rv = FALSE;
		// Пытаемся получить данные
		if ($result = $this->query("SELECT * FROM `" . self::TABLE_NAME_OFFERS . "` WHERE `id` = $id")) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				if ($row = $result->fetch_assoc()) {
					$rv = $row;
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Получение товаров для категории
	public function OffersGetForCategory($id_category, $offset, $limit) {
		// Нормализуем входные данные
		$id_category = $this->IntValue($id_category);
		$offset = $this->IntValue($offset);
		$limit = $this->IntValue($limit);
		
		// Возвращаемый результат
		$rv = array();
		
		// Строим запрос
		$sql_query = "SELECT SQL_CALC_FOUND_ROWS *
				FROM `" . self::TABLE_NAME_OFFERS . "`
				WHERE `id_category` = $id_category
				ORDER BY `added_at` DESC
				LIMIT $offset, $limit";
		
		// Пытаемся получить данные
		if ($result = $this->query($sql_query)) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				while ($row = $result->fetch_assoc()) {
					$rv[] = $row;
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================

	//======================================================================
	// Получение картинок для товара
	public function OfferPicturesGetForOffer($id_offer) {
		// Нормализуем входные данные
		$id_offer = $this->IntValue($id_offer);
		
		// Возвращаемый результат
		$rv = array();
		
		// Строим запрос
		$sql_query = "SELECT `picture`
				FROM `" . self::TABLE_NAME_OFFER_PICTURES . "`
				WHERE `id_offer` = $id_offer AND `disabled` = 0";
		
		// Пытаемся получить данные
		if ($result = $this->query($sql_query)) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				while ($row = $result->fetch_assoc()) {
					$rv[] = $row['picture'];
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================

	//======================================================================
	// Получение случайных товаров
	public function OffersGetRandom($count) {
		// Нормализуем входные данные
		$count = $this->IntValue($count);
		
		// Возвращаемый результат
		$rv = array();
		
		// Строим запрос
		$rand = rand(0, 999);
		$sql_query = "SELECT * FROM `" . self::TABLE_NAME_OFFERS . "` WHERE `rand` = $rand ORDER BY RAND() LIMIT $count";
		
		// Пытаемся получить данные
		if ($result = $this->query($sql_query)) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				while ($row = $result->fetch_assoc()) {
					$rv[] = $row;
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Поиск товаров
	public function OffersSearchForQuery($query, $count) {
		// Нормализуем входные данные
		$count = $this->IntValue($count);
		$query = preg_replace('/[^\pL\pN]+/u', '%', $query);
		$query = preg_replace('/^[^\pL\pN]+/u', '', $query);
		$query = preg_replace('/[^\pL\pN]+$/u', '', $query);
		$query = $this->real_escape_string("%$query%");
		
		// Возвращаемый результат
		$rv = array();
		
		// Строим запрос
		$sql_query = "SELECT * FROM `" . self::TABLE_NAME_OFFERS . "` WHERE `name` LIKE '$query' LIMIT $count";
		
		// Пытаемся получить данные
		if ($result = $this->query($sql_query)) {
			// Если есть результаты
			if ($result->num_rows) {
				// Используем их
				while ($row = $result->fetch_assoc()) {
					$rv[] = $row;
				}
			}
			// Освобождаем ресурсы
			$result->free();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
        //======================================================================
        // Получение списка валют
        public function CurrencyGetList() {
		$rv = array();
		
		$sql_query = "SELECT
				`currency`,
				`rate`,
				`description`,
				UNIX_TIMESTAMP(`last_update`) AS `last_update`
			FROM " . self::TABLE_NAME_CURRENCY_RATE;
		
		if ($result = $this->query($sql_query)) {
			if ($result->num_rows) {
				while ($row = $result->fetch_assoc()) {
					$rv[$row['currency']] = $row;
				}
			}
		}
		return $rv;
        }
        //======================================================================
        
        //======================================================================
        // Обновление курса валюты
        public function CurrencyUpdateRate($currency, $rate, $description) {
		// Препарируем входные данные
		$currency = $this->real_escape_string(mb_strtoupper($currency));
		$description = $this->real_escape_string($description);
		$rate = floatval($rate);
		// Составляем запрос
		$sql_query = "INSERT INTO " . self::TABLE_NAME_CURRENCY_RATE . "
				SET
					`currency` = '$currency',
					`rate` = '$rate',
					`description` = '$description',
					`last_update` = NOW()
				ON DUPLICATE KEY UPDATE
					`rate` = '$rate',
					`description` = '$description',
					`last_update` = NOW()";
		// Выполняем запрос
		$this->query($sql_query);
        }
        //======================================================================

}
