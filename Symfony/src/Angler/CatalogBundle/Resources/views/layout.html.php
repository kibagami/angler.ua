<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $asseticHelper \Symfony\Bundle\AsseticBundle\Templating\AsseticHelper */
/** @var $assetsHelper \Symfony\Component\Templating\Helper\AssetsHelper */
?>
<? $view->extend('AnglerCoreBundle::base.html.php') ?>

<? $view['slots']->start('stylesheets:base') ?>
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerCatalogBundle/Resources/public/css/grid.css',
		'@AnglerCatalogBundle/Resources/public/css/layout.css',
        '@AnglerCatalogBundle/Resources/public/css/header.css',
	),
	array('yui_css'),
	array('output' => 'catalog/css/ui.css')

) as $url
): ?>
<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>

<? foreach ($view['assetic']->stylesheets(
                array(
                    '@AnglerCatalogBundle/Resources/public/css/controls/*.css',
                ),
                array('yui_css'),
                array('output' => 'catalog/css/controls.css')

            ) as $url
): ?>
    <link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>


<? if($view['slots']->has('stylesheets:custom')): ?>
<!-- Include custom stylesheets -->
<? $view['slots']->output('stylesheets:custom') ?>
<!-- /Include custom stylesheets -->
<? endif ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('javascripts:base') /** Include javascript files */ ?>
<? if($view['slots']->has('javascripts:custom')): /** Include custom javascript files */ ?>
<? $view['slots']->output('javascripts:custom') ?>
<? endif ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('body') ?>
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


		<div class="b-grid-row">

			<div class="b-grid-box b-grid-box__filled">

				<div class="b-grid-layout b-grid-box__reducer cfx">
					<div class="b-grid-column b-grid-column__right">
						<!-- Search -->
						<div class="b-search" id="search">
							<form method="post" action="">
								<div class="b-search-box cfx">
									<input type="text" name="search[keyword]" class="b-search-input">
								</div>
							</form>
						</div>
						<!-- /Search -->
					</div>
					<div class="b-grid-layout__wrapper">
						<div class="b-grid-column b-grid-column__center">
							<!-- Navigation -->
							<?= $view->render('AnglerBackendBundle:Default:partial/navigation.html.php') ?>
							<!-- /Navigation -->
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="b-grid-row">
			<div class="b-grid-box">
				<?= $view->render('AnglerBackendBundle:Default:partial/breadcrumbs.html.php') ?>
			</div>
		</div>

		<div class="b-grid-row">
			<div class="b-grid-box">
				<? $view['slots']->output('_content') ?>
			</div>
		</div>
	</div>
</div>

<div class="b-grid-row">
	<div class="b-grid-box b-grid-box__filled" id="footer">
		<div class="b-grid-box__reducer">
			Footer content
		</div>
	</div>
</div>
<script type="text/javascript">
	$.ready(function(){
		window.ui = new UI({ 'default_page' : 'landpage' });

		ui.init();

		$(window).bind('hashchange', function(){
			if (!ui.hist.compare(location.hash)) {
				ui.hist.set(location.hash);
				ui.showPage(ui.hist.parseHash());
			}
		});
		ui.show();
	});
</script>

<? $view['slots']->stop() ?>

