<?
/**
 * @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
 * @var $slotsHelper \Symfony\Component\Templating\Helper\SlotsHelper
 * @var $assetsHelper \Symfony\Component\Templating\Helper\AssetsHelper
 */
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Include stylesheets -->
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerCoreBundle/Resources/public/css/*',
	),
	array('yui_css'),
	array('output' => 'css/styles.css')
) as $url): ?>
	<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>

	<? foreach ($view['assetic']->javascripts(
		array(
			'@AnglerCoreBundle/Resources/public/js/framework/jquery.js',
			'@AnglerCoreBundle/Resources/public/js/framework/knockout-2.2.0.debug.js',
            '@AnglerCoreBundle/Resources/public/js/framework/json2.js',
		),
		array('yui_js'),
		array('output' => 'js/framework.js')
	) as $url): ?>
	<script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
	<? endforeach ?>

<? if($view['slots']->has('stylesheets:base')): ?>
<? $view['slots']->output('stylesheets:base') ?>
<? endif ?>
	<!-- /Include stylesheets -->

	<title><? $view['slots']->output('title', 'Default Title') ?></title>

</head>
<body>

	<? $view['slots']->output('body') ?>

<!-- Include javascript files -->
<? if($view['slots']->has('javascripts:base')):
$view['slots']->output('javascripts:base') ?>
<? endif ?>
<!-- /Include javascript files -->
</body>
</html>
