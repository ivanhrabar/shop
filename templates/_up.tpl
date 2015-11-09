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
<link rel="stylesheet" href="/css/bootstrap-ali.min.css">
<!-- <link rel="stylesheet" href="/css/bootstrap-theme.min.css"> -->
<!--[if lt IE 9]>
	<script src="/js/html5shiv.min.js"></script>
	<script src="/js/respond.min.js"></script>
<![endif]-->
<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
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
				<input type="text" name="q" class="form-control" placeholder="<?php print $Lang->GetString('Search'); ?>" value="<?php if (isset($search_query)) print htmlspecialchars($search_query); ?>">
			</div>
			<button type="submit" class="btn btn-default"><?php print $Lang->GetString('Search'); ?></button>
		</form>
      </div>
</nav>
<div class="page-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
				<h1><a href="/"><img src="/img/sale_tag.gif" style="max-width: 100%;"></a></h1>
			</div>
			<div class="col-md-9">
				<h1>
					Best goods from universe!<br />
					<small>Free shipping, fast check out</small>
				</h1>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default hidden-xs">
				<div class="panel-heading"><?php print $Lang->GetString('Categories'); ?></div>
				<div class="panel-body">
					<div class="list-group">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<a class=\"list-group-item active\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
		else {
			print "<a class=\"list-group-item\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
	}
?>
					</div>
				</div>
			</div>
			
			<div class="panel panel-default hidden-xs">
				<div class="panel-heading"><?php print $Lang->GetString('Powered by:'); ?></div>
				<div class="panel-body">
					<a href="<?php print htmlspecialchars("https://www.epn.bz/inviter?id=" . $settings['deep_link_hash']); ?>" target="_blank"><img src="/img/epn_logo.png" style="width:100%"></a>
				</div>
			</div>
		</div>
		<div class="col-md-9">
