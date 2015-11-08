<?php

// Метаданные страницы
if (!$offer_info) {
	$template_title = "{$settings['site_name']} - " . $Lang->GetString('Good not found.');
	$template_description = "{$settings['site_name']}";
	
}
else {
	$template_title = "{$settings['site_name']} - " . sprintf($Lang->GetString('Buy "%s" for only %s %s.'), $offer_info['name'], $offer_info['price'], $offer_info['currency']);
	$template_description = "{$settings['site_name']} - " . sprintf($Lang->GetString('Buy "%s" from category "%s" for only %s %s.'), $offer_info['name'], $offer_info['category'], $offer_info['price'], $offer_info['currency']);
}
$template_keywords = "china goods, best goods, phones, food, tablets, shirts, {$offer_info['category']}";

include_once $Common->GetTemplatePath('_up');

// Если нет информации о товаре
if (!$offer_info) {
?>
	<div class="alert alert-danger" role="alert"><strong>Problem!</strong> Good not found!</div>
<?php
}
// Если информация о товаре  получена
else {
?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body">
			<?php if ($offer_info['picture'] != '') { ?>
				<div class="row">
					<div class="col-md-2 hidden-xs">
						<ul class="list-group">
						<?php
							foreach ($offer_info['images'] as $image_num => $image_url) {
								print "<li class=\"list-group-item\"><a href=\"#\" onclick=\"javascript:document.getElementById('good_img_main').src = document.getElementById('image_preview_$image_num').src; return false;\">";
								print "<img id=\"image_preview_$image_num\" src=\"" . htmlspecialchars($image_url) . "\" style=\"max-width:100%;\">";
								print "</a></li>\n";
							}
						?>
						</ul>
					</div>
					<div class="col-md-10">
						<p><img id="good_img_main" src="<?php print htmlspecialchars($offer_info['picture']) ;?>" style="max-width:50%;"></p>
					</div>
				</div>
			<?php } ?>
				<p><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?>
				<?php if ($offer_info['category'] != '') { ?>
					<br />
					<strong><?php print $Lang->GetString('Category'); ?></strong>: <a href="<?php print htmlspecialchars($offer_info['category_link']); ?>"><?php print htmlspecialchars($offer_info['category']) ?></a>
				<?php } ?>
				</p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-danger" href="<?php print htmlspecialchars($offer_info['url']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
				</div>
			</div>
		</div>
<?php
	if (sizeof($offers)) {
		print "<h3>" . $Lang->GetString('See also:') . "</h3>\n";
		print "<div class=\"container-fluid\">\n";
		print "<div class=\"row\">\n";
		for ($i = 0; $i < 3; $i++) {
			print "<div class=\"col-md-4\">\n";
			if (isset($offers[$i])) {
	?>
				<div class="panel panel-default">
					<div class="panel-heading"><?php print htmlspecialchars($offers[$i]['name']); ?></div>
					<div class="panel-body">
						<?php if ($offers[$i]['picture'] != '') { ?>
							<p><a href="<?php print htmlspecialchars($offers[$i]['link']); ?>"><img src="<?php print htmlspecialchars($offers[$i]['picture']) ;?>" style="max-width:50%;"></a></p>
						<?php } ?>
						<p><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offers[$i]['price'] . ' ' . $offers[$i]['currency']); ?></p>
						<div class="btn-toolbar" role="toolbar">
							<div class="btn-group">
								<a class="btn btn-danger" href="<?php print htmlspecialchars($offers[$i]['url']) ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
							</div>
							<div class="btn-group">
								<a class="btn btn-default" href="<?php print htmlspecialchars($offers[$i]['link']) ?>"><?php print $Lang->GetString('More info'); ?></a>
							</div>
						</div>
					</div>
				</div>
	<?php
			}
			print "</div>\n";
		}
		print "</div>\n";
		print "</div>\n";
	}
}

include_once $Common->GetTemplatePath('_down');
