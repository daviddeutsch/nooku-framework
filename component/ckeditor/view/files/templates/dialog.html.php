<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>

<?= object('com:ckeditor.controller.file')
    ->container('files-files')
    ->layout('browser')
    ->types(array($type))
    ->editor($state->editor)
    ->render();
?>

<script src="media://ckeditor/js/ckeditor.dialog.js" />

<script>
    window.addEvent('domready', function() {
        document.id('details').adopt(document.id('image-insert-form'));
        new Ckeditor.Dialog({
            type: '<?= $type ?>'
        });
    });
</script>

<div id="image-insert-form">
    <input type="hidden" name="type" id="image-type" value=""/>
    <fieldset>
        <div>
            <label for="image-url"><?= translate('URL') ?></label>
            <div>
                <input type="text" id="image-url" value="" />
            </div>
        </div>
        <?if($type == 'file'):?>
            <div id="link-text">
                <label for="image-text"><?= translate('Text') ?></label>
                <div>
                    <input type="text" id="image-text" value="" />
                </div>
            </div>
        <?endif;?>
        <div>
            <label for="image-alt"><?= translate('Description') ?></label>
            <div>
                <input type="text" id="image-alt" value="" />
            </div>
        </div>
        <div>
            <label for="image-title"><?= translate('Title') ?></label>
            <div>
                <input type="text" id="image-title" value="" />
            </div>
        </div>
        <? if($type == 'image') : ?>
            <div>
                <label for="image-align"><?= translate('Align') ?></label>
                <div>
                    <select size="1" id="image-align" title="<?= translate('Positioning of this image') ?>">
                        <option value="" selected="selected"><?= translate('Not Set') ?></option>
                        <option value="left"><?= translate('Left') ?></option>
                        <option value="right"><?= translate('Right') ?></option>
                    </select>
                </div>
            </div>
        <? endif; ?>
    </fieldset>
</div>