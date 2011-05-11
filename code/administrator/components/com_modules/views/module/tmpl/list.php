<?php 
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @helper('behavior.tooltip') ?>

<form action="<?= @route('layout=form') ?>" method="get" name="adminForm" id="modules-module-list">
	<table class="adminlist" summary="Add Module">
		<thead>
			<tr>
				<th colspan="4">
					<?= @text('Modules') ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<? foreach($modules as $i => $module) : ?>
			<? if(!$i%2) : ?>
				<tr valign="top">
			<? endif; ?>
			<? $last = $i+1 == count($modules) ?>

			<td width="50%">
				<span <?= KHelperArray::toString(array(
					'class' 	=> 'editlinktip hasTip',
					'title'		=> @escape(@text($module->description)),
					'name'		=> 'module',
					'value'		=> $module->module
				)) ?>>
					<input type="radio" name="module" value="<?= $module->module ?>" />
					<a href="<?= @route('layout=form&module='.$module->module) ?>">
						<?= @text(@escape($module->name)) ?>
					</a>
				</span>
			</td>

			<? if($last) : ?> 
				<td width="50%">&nbsp;</td>
			<? endif; ?>
			
			<? if($i%2 || $last) : ?> 
				</tr>
			<? endif; ?>
		<? endforeach ?>
		</tbody>
	</table>
</form>