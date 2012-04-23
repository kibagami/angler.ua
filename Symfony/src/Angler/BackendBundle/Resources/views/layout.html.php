<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Include stylesheets -->
	<? foreach ($view['assetic']->stylesheets(
		array('@AnglerBackendBundle/Resources/public/css/*')) as $url
	): ?>
	<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
	<? endforeach ?>
	<!-- /Include stylesheets -->

	<title><? $view['slots']->output('title', 'Default Title') ?></title>

</head>
<body>
<!-- Fader -->
<div class="hidden fader" id="fader">
	<iframe></iframe>
</div>
<!-- /Fader -->

<!-- Popup -->
<div class="popup hidden" id="popup">
	<div class="popup-inner aligner">
		<div class="popup-header">
			<a class="icon icon-close"></a>
		</div>
		<div class="popup-content"></div>
	</div>
</div>
<!-- /Popup -->

<div class="b-page" id="page">
	<div class="b-page-content">
		<? $view['slots']->output('_content') ?>
	</div>
</div>

<div class="b-grid-row">
	<div class="b-grid-box b-grid-box_filled" id="footer">
		<div class="b-grid-box_reducer">
			Footer content
		</div>
	</div>
</div>
<!-- Include JS Framework -->
<!--<script type="text/javascript" src="/js/framework/jquery.js"></script>-->
<!--<script type="text/javascript" src="/js/interfaces/interface.js?--><?//=$time ?><!--"></script>-->
<!-- /Include JS Framework -->
<!--<script type="text/javascript" src="/js/hashchange.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/location.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/page.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/ui.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/main.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Auth.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Landpage.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Catalog.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Profile.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Articles.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Information.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/pages/Wishlist.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/fader.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript" src="/js/modal.js?--><?//=$time ?><!--"></script>-->
<!--<script type="text/javascript">-->
<!--	ui = new UI({ 'default_page' : 'landpage' });-->
<!---->
<!--	ui.init();-->
<!---->
<!--	$(window).bind('hashchange', function(){-->
<!--		if (!ui.hist.compare(location.hash))-->
<!--		{-->
<!--			ui.hist.set(location.hash);-->
<!--			ui.showPage(ui.hist.parse_hash());-->
<!--		}-->
<!--	});-->
<!---->
<!--	ui.show();-->
<!--</script>-->
</body>
</html>
