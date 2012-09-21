<?php
/**
 * @version        $Id$
 * @package        Nooku_Server
 * @subpackage     Articles
 * @copyright      Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */
defined('KOOWA') or die('Restricted access');
?>

<div class="page-header">
    <h1><?=@text('Change your password');?></h1>
</div>

<form action="" method="post" class="-koowa-form form-horizontal">
    <div class="control-group">
        <label class="control-label" for="password"><?= @text('Password') ?></label>

        <div class="controls">
            <input type="password" id="password" name="password" value=""/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="password"><?= @text('Verify Password') ?></label>

        <div class="controls">
            <input type="password" id="password_verify" name="password_verify"/>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary validate" type="submit"><?= @text('Save') ?></button>
    </div>
    <? if (isset($token)): ?>
    <input type="hidden" name="token" value="<?=$token;?>"/>
    <? endif;?>
    <input type="hidden" name="action" value="<?=isset($token) ? 'reset' : 'save';?>"/>
</form>