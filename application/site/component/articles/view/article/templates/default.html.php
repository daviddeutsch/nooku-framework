<?php
/**
 * @package        Nooku_Server
 * @subpackage     Articles
 * @copyright      Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */
?>

<article <?= !$article->published ? 'class="article-unpublished"' : '' ?>>
    <div class="page-header">
	    <? if ($article->editable) : ?>
	    <a style="float: right;" class="btn" href="<?= @helper('route.article', array('row' => $article, 'layout' => 'form')) ?>">
	        <i class="icon-edit"></i>
	    </a>
	    <? endif; ?>
	    <h1><?= $article->title ?></h1>
	    <?= @helper('date.timestamp', array('row' => $article, 'show_modify_date' => false)); ?>
	    <? if (!$article->published) : ?>
	    <span class="label label-info"><?= @text('Unpublished') ?></span>
	    <? endif ?>
	    <? if ($article->access) : ?>
	    <span class="label label-important"><?= @text('Registered') ?></span>
	    <? endif ?>
	</div>

    <? if($article->thumbnail): ?>
        <img class="thumbnail" src="<?= $article->thumbnail ?>" align="right" style="margin:0 0 20px 20px;" />
    <? endif; ?>

    <? if($article->fulltext) : ?>
    <div class="article__introtext">
        <?= $article->introtext ?>
    </div>
    <? else : ?>
    <?= $article->introtext ?>
    <? endif ?>

    <?= $article->fulltext ?>

    <? if($article->isTaggable()) : ?>
    <?= @template('com:terms.view.terms.default.html', array('terms' => $article->getTerms())) ?>
    <? endif; ?>

    <? if($article->isAttachable()) : ?>
    <?= @template('com:attachments.view.attachments.default.html', array('attachments' => $article->getAttachments(), 'exclude' => array($article->image))) ?>
    <? endif ?>
</article>

<? if($article->id && $article->isCommentable()) : ?>
<div class="comments">
    <?= @object('com:articles.controller.comment')->row($article->id)->render(array('row' => $article));?>
</div>
<? endif ?>
