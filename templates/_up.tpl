<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php
	if (!isset($template_title) || $template_title == '') {
		$template_title = $settings['site_name'];
	}
	print "<title>" . htmlspecialchars($template_title) . "</title>\n";

	if (!isset($template_description) || $template_description == '') {
		$template_description = '';
	}
	print "<meta name=\"description\" content=\"" . htmlspecialchars($template_description) . "\">\n";
	
	if (!isset($template_keywords) || $template_keywords == '') {
		$template_keywords = '';
	}
	print "<meta name=\"keywords\" content=\"" . htmlspecialchars($template_keywords) . "\">\n";
	
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/bootstrap-spacelab.min.css">
<link rel="stylesheet" href="/css/main.css">
<link rel="alternate" title="<?php print htmlspecialchars($settings['site_name']) ?>" href="<?php print htmlspecialchars($Path->Rss()); ?>" type="application/rss+xml">
<!-- <link rel="stylesheet" href="/css/bootstrap-theme.min.css"> -->
<!--[if lt IE 9]>
	<script src="/js/html5shiv.min.js"></script>
	<script src="/js/respond.min.js"></script>
<![endif]-->
<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default new-navbar" role="navigation">
	<div class="container">
		<ul class="nav navbar-nav">
			<li><a href="/"><?php print $Lang->GetString('Main page'); ?></a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php print $Lang->GetString('Categories'); ?><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<li class=\"active\"><a href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
		}
		else {
			print "<li><a href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
		}
	}
?>
				</ul>
			</li>
		</ul>
		<form class="navbar-form navbar-right" role="search" method="get" action="<?php print htmlspecialchars($Path->Search()); ?>">
			<div class="form-group">
				<input type="text" name="q" class="form-control form-control-new-style" placeholder="<?php print $Lang->GetString('Search'); ?>" value="<?php if (isset($search_query)) print htmlspecialchars($search_query); ?>">
			</div>
			<button type="submit" class="btn btn-default btn-search"><?php print $Lang->GetString('Search'); ?></button>
		</form>
      </div>
</nav>
<div class="page-header">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-3 col-xs-3">
				<h1 class="h1-head-logo"><a href="/"><img src="/img/logo.png" class="logo-img"><div class="clearfix"></div></a></h1>
			</div>
			<div class="col-md-9 col-sm-9 col-xs-9">
				<h1 class="header-text">
					<?php print $Lang->GetString('Best goods from universe!'); ?><br />
					<small><?php print $Lang->GetString('Free shipping, fast check out'); ?></small>
				</h1>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default hidden-xs hidden-sm">
				<div class="panel-heading panel-heading-new-style"><?php print $Lang->GetString('Categories'); ?></div>
				<div class="">
					<div class="list-group">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<a class=\"list-group-item link-cat-search active\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
		else {
			print "<a class=\"list-group-item link-cat-search\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
	}
?>
					</div>
				</div>
			</div>
			
			<div class="panel panel-default hidden-xs">
				<div class="panel-heading"><?php print $Lang->GetString('Powered by:'); ?></div>
				<div class="panel-body text-center">
					<a href="<?php print htmlspecialchars("https://www.epn.bz/inviter?id=" . $settings['user_deep_link_hash']); ?>" target="_blank"><img class="img-epn-logo" src="/img/epn_logo.png"></a>
				</div>
			</div>
		</div>
		<div class="col-md-9">
