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

<? $view['slots']->start('body') ?>
<?php if ($error): ?>
<div><?php echo $error->getMessage() ?></div>
<?php endif; ?>

<form action="<?php echo $view['router']->generate('AnglerBackendBundle_login_check') ?>" method="post">
	<label for="username">Username:</label>
	<input type="text" id="username" name="_username" value="<?php echo $last_username ?>" />

	<label for="password">Password:</label>
	<input type="password" id="password" name="_password" />

	<!--
			If you want to control the URL the user is redirected to on success (more details below)
			<input type="hidden" name="_target_path" value="/account" />
		-->

	<button type="submit">login</button>
</form>
<? $view['slots']->stop() ?>

