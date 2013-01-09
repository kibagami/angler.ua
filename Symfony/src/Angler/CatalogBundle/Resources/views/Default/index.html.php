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
		<div class="b-side-menu" id="menu">
			<ul class="b-list">
				<li class="b-list-item">
					<div class="b-side-menu-item b-side-menu-item__current">
						<a href="#buttons"><span>Buttons</span></a>
					</div>
				</li>
				<li class="b-list-item">
					<div class="b-side-menu-item">
						<a href="#dialogs"><span>Dialogs</span></a>
					</div>
				</li>
				<li class="b-list-item">
					<div class="b-side-menu-item">
						<a href="#dates"><span>Date widgets</span></a>
					</div>
				</li>
				<li class="b-list-item">
					<div class="b-side-menu-item">
						<a href="#tabs"><span>Tab</span></a>
					</div>
				</li>
				<li class="b-list-item">
					<div class="b-side-menu-item">
						<a href="#lists"><span>Lists</span></a>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<div class="b-grid-layout__wrapper">
		<div class="b-grid-column__center b-frame b-block__filled">
			<div class="b-frame_title">
				<span>Buttons</span>
			</div>
			<div class="b-frame_content b-reduce_small">
				Content
			</div>
		</div>
	</div>
</div>

