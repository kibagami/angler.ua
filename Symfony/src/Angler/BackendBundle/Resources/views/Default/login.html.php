<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
?>
<? $view->extend('AnglerCoreBundle::base.html.php') ?>

<? $view['slots']->set('title', "angler.ua. Login Page.")?>

<? $view['slots']->start('stylesheets:base') ?>
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerBackendBundle/Resources/public/css/login.css',
		'@AnglerBackendBundle/Resources/public/css/grid.css',
		'@AnglerBackendBundle/Resources/public/css/ui.css',
	),
	array('yui_css'),
	array('output' => 'backend/css/login.css')

) as $url
): ?>
	<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('javascripts:base') ?>
<? foreach ($view['assetic']->javascripts(
	array(
		'@AnglerBackendBundle/Resources/public/js/login.js',
	),
	array('yui_js'),
	array('output' => 'backend/js/login.js')
) as $url
): ?>
	<script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
<? endforeach ?>
<? $view['slots']->stop() ?>

