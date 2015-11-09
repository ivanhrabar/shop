<?php

include_once 'common.php';

// Получаем идентификатор
$id = isset($_GET['id']) ? $Common->IntValue($_GET['id']) : 0;

// Сюда мы будем редиректить пользователя
$redirect_url = '/';

// Пытаемся получить информацию о товаре
$offer_info = $DBAccess->OfferGetByID($id);

// Если информация о товаре была получена
if ($offer_info) {
	// Используем реферальный урл
	$redirect_url = $Path->PrepareRefUrl($offer_info['url']);
}

// Выполняем редирект
Header("Location: $redirect_url", TRUE, 302);

// Для особо тупых браузеров отдадим ещё и немного данных
print "<html>\n<head>\n";
// Некоторые поймут так
print "<meta http-equiv=\"refresh\" content=\"3; url=" . htmlspecialchars($redirect_url) . "\">\n";
print "</head>\n<body>\n";
// А некоторым может понадобиться и вот такой велосипед
print "Page moved <a id=\"mainlink\" href=\"" . htmlspecialchars($redirect_url) . "\">here</a>.";
print "<script type=\"text/javascript\">\n";
print "window.location.href = document.getElementById(\"mainlink\").href;\n";
print "</script>\n";
print "</body>\n</html>\n";
