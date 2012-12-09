<dl>
    <dt><?= $view['form']->label($form, isset($label) ? $label : null) ?></dt>
	<dd>
		<?= $view['form']->errors($form) ?>
		<?= $view['form']->widget($form) ?>
	</dd>
</dl>
