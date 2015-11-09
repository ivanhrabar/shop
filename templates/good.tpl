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
	<div class="alert alert-danger" role="alert"><strong><?php print $Lang->GetString('Problem!'); ?></strong> <?php print $Lang->GetString('Good not found.'); ?></div>
<?php
}
// Если информация о товаре  получена
else {
?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body">
			<?php if (isset($offer_info['all_images']) && is_array($offer_info['all_images'])) { ?>
				<div class="row">
					<div class="col-md-2 hidden-xs">
						<ul class="list-group">
						<?php
							foreach ($offer_info['all_images'] as $image_num => $image_url) {
								print "<li class=\"list-group-item\"><a href=\"#\" onclick=\"javascript:document.getElementById('good_img_main').src = document.getElementById('image_preview_$image_num').src; return false;\">";
								print "<img id=\"image_preview_$image_num\" src=\"" . htmlspecialchars($image_url) . "\" style=\"max-width:100%;\">";
								print "</a></li>\n";
							}
						?>
						</ul>
					</div>
					<div class="col-md-10">
						<p><img id="good_img_main" src="<?php print htmlspecialchars($offer_info['all_images'][0]) ;?>" style="max-width:50%;"></p>
					</div>
				</div>
			<?php } elseif ($offer_info['picture'] != '') { ?>
				<p><img src="<?php print htmlspecialchars($offer_info['picture']) ;?>" style="max-width:50%;"></p>
			<?php } ?>
				<p><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?>
				<?php if ($offer_info['category'] != '') { ?>
					<br />
					<strong><?php print $Lang->GetString('Category'); ?></strong>: <a href="<?php print htmlspecialchars($offer_info['category_link']); ?>"><?php print htmlspecialchars($offer_info['category']) ?></a>
				<?php } ?>
				</p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-b" href="<?php print htmlspecialchars($offer_info['url']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
				</div>
			</div>
		</div>
<?php
	if (sizeof($offers)) {
		print "<h3>" . $Lang->GetString('See also:') . "</h3>\n";
		print "<div>\n";
		print "<div class=\"row\">\n";
		for ($i = 0; $i < 3; $i++) {
			print "<div class=\"col-md-4 col-sm-6 col-xs-12 wr-good\">\n";
			if (isset($offers[$i])) {
	?>
				<div class="panel panel-default">
					<div class="panel-heading name-good"><?php print htmlspecialchars($offers[$i]['name']); ?></div>
					<div class="panel-body text-center">
						<?php if ($offers[$i]['picture'] != '') { ?>
							<p class="wr-img"><a href="<?php print htmlspecialchars($offers[$i]['link']); ?>"><img class="good-img" src="<?php print htmlspecialchars($offers[$i]['picture']) ;?>"></a></p>
						<?php } ?>
						<p class="text-left text-cat-price"><strong><?php print $Lang->GetString('Price'); ?></strong>: <span class="price-style"><?php print htmlspecialchars($offers[$i]['price'] . ' ' . $offers[$i]['currency']); ?></span></p>
						<div class="btn-toolbar" role="toolbar">
							<div class="btn-group">
								<a class="btn btn-b" href="<?php print htmlspecialchars($offers[$i]['url']) ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
							</div>
							<div class="wr-btn-more-info">
								<a class="btn btn-more-info" href="<?php print htmlspecialchars($offers[$i]['link']) ?>"><?php print $Lang->GetString('More info'); ?></a>
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
