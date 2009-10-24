<? /** $Id: default.php 299 2009-10-24 00:19:50Z johan $ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<? @script(@$mediaurl.'/com_terms/js/terms.js') ?>

<div id="terms-panel">
	<form action="<?= @route(); ?>" method="post" id="terms-form">
		<input type="hidden" name="row_id"     value="<?= @$state->row_id?>" />
		<input type="hidden" name="table_name" value="<?= @$state->table_name?>" />
		<table class="adminlist" style="clear: boyth;">
		<thead>
			<tr>
				<th><?= @text('Tag')?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<? $m = 0; ?>
			<? foreach (@$terms as $term) : ?>
			<tr class="<?php echo 'row'.$m; ?>">
				<td align="center">
					<?= $term->name; ?>
				</td>
				<td align="center">
					<a class="tags-button" rel="<?= http_build_query($term->getData(), '', '&amp;') ?>" onclick="Terms.execute('delete', this.rel)"><?= @text('Remove') ?></a/>
				</td>
			</tr>
			<? $m = (1 - $m); ?>
			<? endforeach; ?>

			<tr class="<?php echo 'row'.$m; ?>">
				<td align="center" colspan="2">
					<input name="name" value="" style="width: 80%"/>
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>