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
	print "<h1 align=\"center\">" . sprintf($Lang->GetString('Category &laquo;%s&raquo;'), htmlspecialchars($current_category['title']));
	if ($page_count > 1) {
		print "<br /><small>" . sprintf($Lang->GetString('Page %d from %d'), $page, $page_count) . "</small>";
	}
	print "</h1>";
	
	foreach ($offers as $offer_info) {
?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body">
			<?php if ($offer_info['picture'] != '') { ?>
				<p><a href="<?php print htmlspecialchars($offer_info['link']); ?>"><img src="<?php print htmlspecialchars($offer_info['picture']); ?>" style="max-width:50%;"></a></p>
			<?php } ?>
				<p><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?></p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-danger" href="<?php print htmlspecialchars($offer_info['url']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
					<div class="btn-group">
						<a class="btn btn-default" href="<?php print htmlspecialchars($offer_info['link']); ?>"><?php print $Lang->GetString('More info'); ?></a>
					</div>
				</div>
			</div>
		</div>
<?php
	}
	
	if (sizeof($pages)) {
		print "<ul class=\"pagination\">\n";
		foreach ($pages as $page_data) {
			if ($page_data['link'] != '') {
				print "<li><a href=\"" . htmlspecialchars($page_data['link']) . "\">" . htmlspecialchars($page_data['page']) . "</a></li>\n";
			}
			else {
				print "<li class=\"active\"><span>" . htmlspecialchars($page_data['page']) . " <span class=\"sr-only\">(current)</span></a></li>\n";
			}
		}
		print "</ul>\n";
	}
}


include_once $Common->GetTemplatePath('_down');
