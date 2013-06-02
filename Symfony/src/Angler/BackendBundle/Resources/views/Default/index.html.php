<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
?>
<? $view->extend('AnglerBackendBundle::layout.html.php') ?>

<? $view['slots']->set('title', "angler.ua. Administration Panel.")?>


<? $view['slots']->start('stylesheets:custom') ?>
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerBackendBundle/Resources/public/css/header.css',
		'@AnglerBackendBundle/Resources/public/css/product.css',
	),
	array('yui_css'),
	array('output' => 'backend/css/homepage.css')

) as $url
): ?>
	<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>
<? $view['slots']->stop() ?>

<? $view['slots']->start('javascripts:custom') ?>
<? foreach ($view['assetic']->javascripts(
	array(
		'@AnglerBackendBundle/Resources/public/js/pages/*',
	),
	array('yui_js'),
	array('output' => 'backend/js/homepage.js')
) as $url
): ?>
<script type="text/javascript" src="<?= $view->escape($url) ?>"></script>
<? endforeach ?>
<? $view['slots']->stop() ?>


<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $form \Symfony\Component\Form\FormView */
/** @var $formHelper \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper */

$formHelper = $view['form']

?>
<? $formHelper->setTheme($form, "AnglerBackendBundle:Form")?>
