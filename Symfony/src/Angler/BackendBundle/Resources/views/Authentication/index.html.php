<?
/**
 * @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
 * @var $error \Symfony\Component\Security\Core\Exception\BadCredentialsException
 * @var $slotsHelper \Symfony\Component\Templating\Helper\SlotsHelper
 */
$slotsHelper = $view['slots'];

$view->extend('AnglerCoreBundle::base.html.php')
?>

<? $slotsHelper->set('title', "angler.ua. Login Page.")?>

<? $slotsHelper->start('stylesheets:base') ?>
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerBackendBundle/Resources/public/css/grid.css',
		'@AnglerBackendBundle/Resources/public/css/ui.css',
	),
	array('yui_css'),
	array('output' => 'catalog/css/styles.css')
) as $url
): ?>
	<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('javascripts:base') ?>
<? foreach ($view['assetic']->javascripts(
	array(
		'@AnglerCatalogBundle/Resources/public/js/app.js',
	),
	array('yui_js'),
	array('output' => 'backend/js/login.js')
) as $url
): ?>
	<script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
<? endforeach ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('body') ?>
<div id="page" class="b-page">
	<div id="auth" class="b-auth-form b-tile">
		<div class="b-reduce b-reduce_middle">
			<div id="greeting">
				<span class="b-bold upper text">Sign in to Angler.ua</span>
			</div>

			<? if ($error): ?>
			<div><?= $error->getMessage() ?></div>
			<? endif ?>

			<div id="login-form">
				<form action="<?= $view['router']->generate('angler_backend_login_check') ?>" method="POST">
					<dl class="b-auth-row">
						<dt>
							<label for="username">Username:</label>
						</dt>
						<dd>
							<input class="b-auth-input b-auth-input__text" type="text" id="username" name="_username" value="<?= $view->escape($last_username) ?>"/>
						</dd>
					</dl>
					<dl class="b-auth-row">
						<dt>
							<label for="password">Password:</label>
						</dt>
						<dd>
							<input class="b-auth-input b-auth-input__password" type="password" id="password" name="_password"/>
						</dd>
					</dl>

					<table class="wide">
					<tr>
						<td>
							<div class="b-choice" id="keep-me">
								<input type="checkbox" id="remember-me" name="_remember_me"/>
								<label for="remember-me">Remember me:</label>
							</div>
						</td>
						<td class="b-ar">
							<button class="b-button b-button__action upper" type="submit">Login</button>
						</td>
					</tr>
					</table>

				</form>
			</div>
		</div>
	</div>

</div>
<? $view['slots']->stop() ?>

