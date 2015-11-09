<?php

$template_title = "{$settings['site_name']} - Best goods from universe!";
$template_description = "{$settings['site_name']} - Best goods from universe! Free shipping, fast check out!";
$template_keywords = "china goods, best goods, phones, food, tablets, shirts";

include_once $Common->GetTemplatePath('_up');

	print "<h1 align=\"center\">Best price</h1>";

	foreach ($offers as $offer_info) {
?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body">
			<?php if ($offer_info['picture'] != '') { ?>
				<p><a href="<?php print htmlspecialchars($offer_info['link']); ?>"><img src="<?php print htmlspecialchars($offer_info['picture']) ;?>" style="max-width:50%;"></a></p>
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
					<div class="btn-group">
						<a class="btn btn-default" href="<?php print htmlspecialchars($offer_info['link']); ?>"><?php print $Lang->GetString('More info'); ?></a>
					</div>
				</div>
			</div>
		</div>
<?php
	}

include_once $Common->GetTemplatePath('_down');

