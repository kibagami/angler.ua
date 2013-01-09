<?
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $slotsHelper \Symfony\Component\Templating\Helper\SlotsHelper */
/** @var $assetsHelper \Symfony\Component\Templating\Helper\AssetsHelper */
?>
<?
$view->extend('AnglerCoreBundle::base.html.php');
$slotsHelper = $view['slots'];
?>

<? $slotsHelper->start('stylesheets:base') ?>
<? foreach ($view['assetic']->stylesheets(
	array(
		'@AnglerCatalogBundle/Resources/public/css/grid.css',
		'@AnglerCatalogBundle/Resources/public/css/ui.css',
		'@AnglerCatalogBundle/Resources/public/css/layout.css',
	),
	array('yui_css'),
	array('output' => 'catalog/css/ui.css')

) as $url
): ?>
<link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>

<? foreach ($view['assetic']->stylesheets(
                array(
                    '@AnglerCatalogBundle/Resources/public/css/blocks/*.css',
                    '@AnglerCatalogBundle/Resources/public/css/controls/*.css',
                ),
                array('yui_css'),
                array('output' => 'catalog/css/controls.css')

            ) as $url
): ?>
    <link rel="stylesheet" href="<?= $view->escape($url) ?>" />
<? endforeach ?>


<? if($slotsHelper->has('stylesheets:custom')): ?>
<!-- Include custom stylesheets -->
<? $slotsHelper->output('stylesheets:custom') ?>
<!-- /Include custom stylesheets -->
<? endif ?>
<? $slotsHelper->stop() ?>

<? $slotsHelper->start('javascripts:base') /** Include javascript files */ ?>
<? if($slotsHelper->has('javascripts:custom')): /** Include custom javascript files */ ?>
<? $slotsHelper->output('javascripts:custom') ?>
<? endif ?>
<? $slotsHelper->stop() ?>

<? $slotsHelper->start('body') ?>
<!-- Fader -->
<div class="hidden fader" id="fader"><iframe></iframe></div>
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
            </div>

		</div>


		<div class="b-grid-row">

			<div class="b-grid-box">

				<div class="b-grid-layout b-grid-box__reducer cfx">
					<div class="b-grid-column b-grid-column__right">
						<table class="b-dropdown-holder">
							<tr>
								<td><label for="view-selector">Select a view:</label></td>
								<td>
									<select name="view" id="view-selector">
										<option value="0">Controls</option>
										<option value="1">Calculator</option>
									</select>
								</td>
							</tr>
						</table>
					</div>
					<div class="b-grid-layout__wrapper">
						<div class="b-grid-column b-grid-column__center">
							<div class="b-page-title">
								<span>Controls Explorer</span>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="b-grid-row">
			<div class="b-grid-box">
				<? $slotsHelper->output('_content') ?>
			</div>
		</div>
	</div>
</div>

<div class="b-grid-row">
	<div class="b-grid-box b-footer">
		Footer content
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

<? $slotsHelper->stop() ?>

