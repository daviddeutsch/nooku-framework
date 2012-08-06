<?php
/**
 * @version     $Id: form.php 3035 2011-10-09 16:57:12Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

defined('KOOWA') or die('Restricted access') ?>

<script src="media://com_pages/js/widget.js" />
<script src="media://com_pages/js/page.js" />

<style src="media://com_pages/css/page-form.css" />

<script>
    function checksubmit(form)
    {
        var submitOK=true;
        var checkaction=form.action.value;
        // do field validation
        if (checkaction=='cancel') {
            return true;
        }
        if (form.title.value == ""){
            alert( "<?= @text( 'Page must have a title', true ); ?>" );
            submitOK=false;
            // remove the action field to allow another submit
            form.action.remove();
        }
        return submitOK;
    }

    window.addEvent('domready', function(){
        $$('.widget').widget({cookie: 'widgets-page'});

        new Page(<?= json_encode(array('active' => $state->type['option'])) ?>);
    });
</script>

<?= @template('com://admin/default.view.form.toolbar') ?>

<form action="" method="post" class="-koowa-form">
    <input type="hidden" name="pages_menu_id" value="<?= $state->menu ?>" />

    <?= @template('form_types') ?>

    <? if($state->type) : ?>
    <div id="main">
        <div class="title">
        	<input type="text" name="title" placeholder="<?= @text('Title') ?>" value="<?= $page->title ?>" size="50" maxlength="255" />
            <br />
            <?= @text('Visitors can access this page at'); ?>
            <?= dirname(JURI::base()) ?>/<input type="text" name="slug" placeholder="<?= @text('Alias') ?>" value="<?= $page->slug ?>" maxlength="255" />
        </div>
        <?= @helper('tabs.startPane', array('id' => 'pane_1')); ?>
        <?= @helper('tabs.startPanel', array('title' => 'General')); ?>
            <?= @template('form_general') ?>
        <?= @helper('tabs.endPanel'); ?>
        <? if($state->type['name'] == 'component') : ?>
            <?= @template('form_component') ?>
            <?= @template('form_page') ?>
        <? endif ?>
        <?= @template('form_modules') ?>
        <?= @helper('tabs.endPane') ?>
    </div>
    <? endif ?>
</form>