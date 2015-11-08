<?php
// Класс для работы с ЦБ РФ
class clCBRF {
	public $currency_hash = array();
	
	//======================================================================
	// Конструктор
	// На входе - хэш с информацией о курсах, полученный из БД
	public function __construct($currency_hash = array()) {
		$this->currency_hash = $currency_hash;
	}
	//======================================================================
	
	//======================================================================
	// Получение обновлённых данных
	public function UpdateRates() {
		// Пробуем получить данные из ЦБ РФ
		$co = curl_init("http://www.cbr.ru/scripts/XML_daily_eng.asp");
		curl_setopt($co, CURLOPT_RETURNTRANSFER, TRUE);
		$cbrf_xml = curl_exec($co);
		// Если облом
		if (curl_errno($co) != CURLE_OK) {
			// Убираем за собой
			curl_close($co);
			// Завершаем работу
			return $this->currency_hash;
		}
		// Если же всё хорошо
		// Убираем за собой
		curl_close($co);
		
		// Парсим инфу
		$xml = simplexml_load_string($cbrf_xml);
		$json = json_encode($xml);
		$cbr_data = json_decode($json, TRUE);

		// Если что-то не так
		if (!is_array($cbr_data) || !isset($cbr_data['Valute'])) {
			// Завершаем работу
			return $this->currency_hash;
		}
		
		// Собираем промежуточную структуру
		$currency_data = array();
		foreach ($cbr_data['Valute'] as $cbr_item) {
			$currency_data[$cbr_item['CharCode']] = array(
				'rate' => str_replace(',', '.', $cbr_item['Value']) / $cbr_item['Nominal'],
				'description' => $cbr_item['Name'],
				'currency' => $cbr_item['CharCode'],
			);
		}

		// Вот эти валюты нам нужны. Остальное пофиг
		$need_rate_for = array(
				'USD',
				'EUR',
				'UAH',
				'BYR',
				'KZT',
				'CNY',
			);
		// Перебираем валюты
		foreach ($need_rate_for as $currency) {
			// Если есть данные
			if (isset($currency_data[$currency])) {
				// Обновляем данные
				$this->currency_hash[$currency] =  array(
						'rate' => $currency_data[$currency]['rate'],
						'description' => $currency_data[$currency]['description'],
						'last_update' => time(),
					);
			}
		}
		// На всякий случай обновим данные о рубле
		$this->currency_hash['RUR'] = array(
				'rate' => 1,
				'description' => 'Russian Ruble',
				'last_update' => time(),
			);
		// Возвращаем результат
		return $this->currency_hash;
	}
	//======================================================================
	
	//======================================================================
	// Нужно ли запросить новые данные с ЦБ РФ
	public function NeedUpdate() {
		// Ничего не известно о рубле? Не порядок!
		if (!isset($this->currency_hash['RUR'])) return TRUE;
		
		// Не понятно какой свежести данные? Не порядок!
		if (!isset($this->currency_hash['RUR']['last_update'])) return TRUE;
		
		// Данные древнее суток? Не порядок!
		if ($this->currency_hash['RUR']['last_update'] + 60*60*24 < time()) return TRUE;
		
		// Во всех остальных случая обновляться не надо!
		return FALSE;
	}
	//======================================================================
	
	//======================================================================
	// Функция конвертации валюты
	public function Convert($sum, $src_currency, $dst_currency) {
		// Возвращаемый результат
		$rv = array(
			'sum' => $sum,
			'currency' => $src_currency,
		);
		
		// Нормализуем входные данные (на всякий случай)
		$src_currency = mb_strtoupper($src_currency);
		$dst_currency = mb_strtoupper($dst_currency);
		
		// Если вход и выход совпадают
		if ($src_currency == $dst_currency) return $rv;
		
		// Если не известен курс исходной валюты
		if (!isset($this->currency_hash[$src_currency])) return $rv;
		
		// Если не известен курс валюты назначения
		if (!isset($this->currency_hash[$dst_currency])) return $rv;
		
		// Сначала переводим сумму в рубли
		$sum_rur = $sum * $this->currency_hash[$src_currency]['rate'];
		// Переводим рубли валюту назначения и формируем результат
		$rv = array(
			'sum' => round($sum_rur / $this->currency_hash[$dst_currency]['rate'], 2),
			'currency' => $dst_currency,
		);
		
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
}
