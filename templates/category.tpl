<?php
// Метаданные страницы
if (!$current_category) {
	$template_title = "{$settings['site_name']} - " . $Lang->GetString('Category not found!');
	$template_description = "{$settings['site_name']}";
}
else {
	$template_title = "{$settings['site_name']} - " . sprintf($Lang->GetString('Goods for category "%s" page %d.'), $current_category['title'], $page);
	$template_description = "{$settings['site_name']} - " . sprintf($Lang->GetString('Goods for category "%s" ("%d").'), $current_category['title'], $current_category['count']);
}
$template_keywords = "china goods, best goods, phones, food, tablets, shirts";

include_once $Common->GetTemplatePath('_up');

// Если нет информации о текущей категории, значит что-то не так
if (!$current_category) {
?>
	<div class="alert alert-danger" role="alert"><strong><?php print $Lang->GetString('Problem!'); ?></strong> <?php print $Lang->GetString('Category not found!'); ?></div>
<?php
}
// Если информация о категории есть то всё хорошо
else {
?>
<?php
	print "<div class=\"col-md-12\"><h1 class=\"name-category\" align=\"center\">" . sprintf($Lang->GetString('Category &laquo;%s&raquo;'), htmlspecialchars($current_category['title']));
	if ($page_count > 1) {
		print "<small>" . sprintf($Lang->GetString('Page %d from %d'), $page, $page_count) . "</small><div class=\"clearfix\"></div>";
	}
	print "</h1></div><div class=\"col-md-12\"><div class=\"wr-btn-category-price\">";
	print "<div class=\"btn-group\">\n";
	foreach ($orders as $order_variant) {
		if ($order_variant['current']) {
			print "<a" . ($order_variant['nofollow'] ? ' rel="nofollow"' : '') . " href=\"" . htmlspecialchars($order_variant['url']) . "\" class=\"btn btn-price-category active\">" . htmlspecialchars($order_variant['title']) . "</a>\n";
		}
		else {
			print "<a" . ($order_variant['nofollow'] ? ' rel="nofollow"' : '') . " href=\"" . htmlspecialchars($order_variant['url']) . "\" class=\"btn btn-price-category\">" . htmlspecialchars($order_variant['title']) . "</a>\n";
		}
	}
	print "</div></div></div>\n";
	
	foreach ($offers as $offer_info) {
?>
	<div class="col-md-4 col-sm-6 col-xs-12 wr-good">
		<div class="panel panel-default">
			<div class="panel-heading name-good"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body text-center">
			<?php if ($offer_info['picture'] != '') { ?>
				<p class="wr-img"><a href="<?php print htmlspecialchars($offer_info['link']); ?>"><img class="good-img" src="<?php print htmlspecialchars($offer_info['picture']); ?>"></a></p>
			<?php } ?>
				<p class="text-left text-cat-price"><strong><?php print $Lang->GetString('Price'); ?></strong>: <span class="price-style"><?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?></span></p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-b" href="<?php print htmlspecialchars($offer_info['url']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
					<div class="wr-btn-more-info">
						<a class="btn btn-more-info" href="<?php print htmlspecialchars($offer_info['link']); ?>"><?php print $Lang->GetString('More info'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	}
	
	if (sizeof($pages)) {
		print "<div class=\"col-md-12\"><ul class=\"pagination\">\n";
		foreach ($pages as $page_data) {
			if ($page_data['link'] != '') {
				print "<li><a" . ($order_line != '' ? ' rel="nofollow"' : '') . " href=\"" . htmlspecialchars($page_data['link']) . "\">" . htmlspecialchars($page_data['page']) . "</a></li>\n";
			}
			else {
				print "<li class=\"active\"><span>" . htmlspecialchars($page_data['page']) . " <span class=\"sr-only\">(current)</span></a></li>\n";
			}
		}
		print "</ul></div>\n";
	}
}


include_once $Common->GetTemplatePath('_down');
