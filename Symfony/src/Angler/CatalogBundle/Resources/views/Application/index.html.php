<?
/**
 * @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
 * @var $slotsHelper \Symfony\Component\Templating\Helper\SlotsHelper
 */

$view->extend("AnglerCatalogBundle::layout.html.php");
?>

<? $view['slots']->set('title', "angler.ua. Wellcome to Catalog of Products.")?>

<? $view['slots']->start('stylesheets:custom') ?>
<? foreach ($view['assetic']->stylesheets(
    array(
        '@AnglerCatalogBundle/Resources/public/css/main.css',
        ),
        array('yui_css'),
        array('output' => 'catalog/css/application.css')
    ) as $url
): ?>
    <link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('javascripts:custom') ?>
<? foreach ($view['assetic']->javascripts(
                array(
//                    '@AnglerCatalogBundle/Resources/public/js/Controls/*',
//                    '@AnglerCatalogBundle/Resources/public/js/Views/*',
                    '@AnglerCatalogBundle/Resources/public/js/*',
                ),
                array('yui_js'),
                array('output' => 'catalog/js/application.js')
            ) as $url
): ?>
    <script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
<? endforeach ?>
<? $view['slots']->stop() ?>

<div class="b-grid-layout b-grid-layout__left">
	<div class="b-grid-column__left">
		<div class="b-sidebar" id="menu">
			<!-- ko foreach: tabs -->
			<a class="b-sidebar_item" data-bind="attr: { href: $data.id }, event: { click: $parent.select }">
				<span data-bind="text: $data.title"></span>
			</a>
			<!-- /ko -->
		</div>
	</div>
	<div class="b-grid-layout__wrapper">
		<div class="b-grid-column__center b-frame b-block__filled" id="frame-data">
			<!-- ko with: currentTab -->
			<div class="b-frame_title">
				<span data-bind="text: $data.title"></span>
			</div>
			<div class="b-frame_content b-reduce_small" data-bind="view: $data.view"></div>
			<!-- /ko -->
		</div>
	</div>
</div>

