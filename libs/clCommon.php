<?php
// Здесь описываем все те функции, которые больше негде описать

class clCommon {
	// Конструктор
	public function __construct() {
	}
	// Получение пути к шаблону
	function GetTemplatePath($tpl) {
		$rv = dirname(__FILE__) . '/../templates/' . $tpl . '.tpl';
		return $rv;
	}
}
