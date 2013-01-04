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

