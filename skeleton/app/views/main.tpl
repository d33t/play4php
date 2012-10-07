{block name=captures}{/block}
<!DOCTYPE html>
<html lang="{block name=lang}en{/block}">
    <head>
        <meta charset="utf-8">
        {block name=addHead}{/block}
        <title>{block name=title}Default Page Title{/block}</title>
        <meta name="description" content="{block name=description}{/block}">
        <meta name="keywords" lang="{block name=lang}en{/block}" content="{block name=keywords}{/block}">
        <meta name="author" content="Rusi Rusev (d33t)">
        <!-- http://twitter.github.com/bootstrap/-->
        <link rel="stylesheet" type="text/css" href="/css/bootstrap/2.0.3/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/css/main.css">
        <!-- additional CSS -->
		{block name=addCSS}{/block}
        <script type="text/javascript" src="/js/jquery/jquery-1.7.1.min.js"></script>
        {block name=addJS}{/block}
	</head>
	<body style="padding-top:60px;">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
			  <div class="container">
				<a class="brand" href="/">{nocache}{block name=brand}{$smarty.server.SERVER_NAME|regex_replace:"/^www\./":""|capitalize:true}{/block}{/nocache}</a>
				<div class="nav-collapse">
					<ul class="nav nav-pills">
						{if !isset($smarty.capture.hideIndex)}
						<li{if isset($smarty.capture.currentPage) && $smarty.capture.currentPage == 'index'} class="active"{/if}><a href="/">Home</a></li>
						{/if}
						{block name=addMenus}{/block}
						{block name=addMoreMenusWrapper}
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">More<b class="caret"></b></a>
							<ul class="dropdown-menu">
							{block name=addMoreMenus}{/block}
							</ul>
						</li>
						{/block}
					</ul>
				</div>
			  </div>
			</div><!-- /navbar-inner -->
		</div>
		<div class="container">
			{block name=body}{/block}
			<hr>
			<footer>
				<p>Copyright &copy; 2011-{nocache}{$smarty.now|date_format:"%Y"}{/nocache} Rusi Rusev (d33t) </p>
			</footer>
		</div>
	</body>
</html>