<?php
// Объект для работы с путями
class clPath {
	private $deep_link_hash = '';
	
	// Пути 
	const PATH_OFFER    = '/good';
	const PATH_CATEGORY = '/category';
	const PATH_GO       = '/redirect';
	const PATH_SEARCH   = '/search';
	
	//======================================================================
	// Конструктор
	public function __construct($deep_link_hash) {
		$this->deep_link_hash = $deep_link_hash;
	}
	//======================================================================
	
	//======================================================================
	// Создание строки для использования в урлах
	private function GetSafeString($s) {
		$s = mb_strtolower($s, 'UTF-8');
		$s = mb_substr($s, 0, 255, 'UTF-8');
		$s = preg_replace('/[^\pL\pN]+/u', '-', $s);
		$s = preg_replace('/^[^\pL\pN]+/u', '', $s);
		$s = preg_replace('/[^\pL\pN]+$/u', '', $s);
		return $s;
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
        // Путь к товару
	public function Offer($id, $title = '') {
		$rv = self::PATH_OFFER . '/' . $this->IntValue($id);
		if ($title != '') $rv .= '-' . rawurlencode($this->GetSafeString($title));
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Путь на редирект-скрипт
	public function Go($id) {
		return self::PATH_GO . '/' . $this->IntValue($id);
	}
	//======================================================================
	
	//======================================================================
	// Путь к категории
	public function Category($id, $title = '', $page = 1) {
		$rv = self::PATH_CATEGORY . '/' . $this->IntValue($id);
		if ($title != '') $rv .= '-' . rawurlencode($this->GetSafeString($title));
		if ($page > 1) $rv .= '/' . $this->IntValue($page);
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Путь к поиску
	public function Search() {
		return self::PATH_SEARCH;
	}
	//======================================================================
	
	//======================================================================
	// Подготовка реферальной ссылки
	public function PrepareRefUrl($url) {
		return str_replace('__DEEPLINK-HASH__', $this->deep_link_hash, $url);
	}
	//======================================================================
}
