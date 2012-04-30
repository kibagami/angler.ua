<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $asseticHelper \Symfony\Bundle\AsseticBundle\Templating\AsseticHelper */
/** @var $assetsHelper \Symfony\Component\Templating\Helper\AssetsHelper */

?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Include stylesheets -->
	<? foreach ($view['assetic']->stylesheets(
		array('@AnglerCoreBundle/Resources/public/css/*')
) as $url
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
		<div class="b-grid-row">
			<div class="b-grid-box b-header">

				<div class="b-logo" id="logo">
					<img src="<?= $view['assets']->getUrl('bundles/anglerbackend/images/logotype.png')?>" alt="">
				</div>

				<div class="b-head-layout">

					<div class="b-grid-cell cfx" id="toolbar">
						<ul class="b-vlist b-fr cfx" id="settings">
							<li class="b-vlist-item">
								<div id="cart" class="b-tool-item b-tool-plate b-tile">
									<div class="b-switcher">
										<em>Cart: empty</em>
									</div>
								</div>
							</li>
							<li class="b-vlist-item">
								<div class="b-tool-item b-tool-plate b-tile" id="currency">
									<div class="b-switcher b-switcher_dropdown lang danish">
										<i class="flag"></i>
										<em>Danske Krone</em>
									</div>
								</div>
							</li>
							<li class="b-vlist-item last">
								<div class="b-tool-item b-tool-plate b-tile" id="lang">
									<div class="b-switcher b-switcher_dropdown lang danish">
										<i class="flag"></i>
										<em>Danish</em>
									</div>
									<div class="slide-pan" style="display: none">
								<span class="slide-item lang ukraine">
									<i class="flag"></i>
									<em>����������</em>
								</span>
								<span class="slide-item lang danish">
									<i class="flag"></i>
									<em>Danish</em>
								</span>
								<span class="slide-item lang german">
									<i class="flag"></i>
									<em>German</em>
								</span>
								<span class="slide-item lang svenska">
									<i class="flag"></i>
									<em>Svenska</em>
								</span>
								<span class="slide-item lang norway">
									<i class="flag"></i>
									<em>Norway</em>
								</span>
								<span class="slide-item lang russian">
									<i class="flag"></i>
									<em>�������</em>
								</span>
									</div>
								</div>
							</li>
						</ul>
						<ul class="b-vlist b-fl cfx" id="userpanel">
							<li class="b-vlist-item">
								<a href="#auth?mode=login" class="b-tool-item b-tile_login b-auth-link" id="login">
									<span>Log In</span>
								</a>
							</li>
							<li class="b-vlist-item">
								<div class="b-tool-label">
									<span>or</span>
								</div>
							</li>
							<li class="b-vlist-item">
								<a href="#auth?mode=register" class="b-tool-item b-tile_registration b-auth-link" id="registration">
									<span>Register</span>
								</a>
							</li>
						</ul>
						<b class="clear"></b>
					</div>

				</div>

			</div>
		</div>
		<!-- /Toolbar -->

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


<!-- Include stylesheets -->
<? foreach ($view['assetic']->javascripts(
	array(
		'@AnglerCoreBundle/Resources/public/js/framework/jquery.js',
		'@AnglerCoreBundle/Resources/public/js/framework/json2.js',
		'@AnglerBackendBundle/Resources/public/js/hashchange.js',
		'@AnglerBackendBundle/Resources/public/js/location.js',
		'@AnglerBackendBundle/Resources/public/js/interfaces/*',
		'@AnglerBackendBundle/Resources/public/js/page.js',
		'@AnglerBackendBundle/Resources/public/js/ui.js',
		'@AnglerBackendBundle/Resources/public/js/main.js',
		'@AnglerBackendBundle/Resources/public/js/fader.js',
		'@AnglerBackendBundle/Resources/public/js/modal.js',
		'@AnglerBackendBundle/Resources/public/js/pages/*',
	)
) as $url
): ?>
<script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
<? endforeach ?>
<!-- /Include stylesheets -->

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
<script type="text/javascript">
	window.ui = new UI({ 'default_page' : 'landpage' });

	ui.init();

	$(window).bind('hashchange', function(){
		if (!ui.hist.compare(location.hash)) {
			ui.hist.set(location.hash);
			ui.showPage(ui.hist.parse_hash());
		}
	});
	ui.show();
</script>
</body>
</html>
