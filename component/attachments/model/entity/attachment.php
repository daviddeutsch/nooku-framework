<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Attachments;

use Nooku\Library;

/**
 * Attachment Database Row
 *
 * @author  Steven Rombauts <https://nooku.assembla.com/profile/stevenrombauts>
 * @package Nooku\Component\Attachments
 */
class ModelEntityAttachment extends Library\ModelEntityRow
{
    public function save()
    {
        $return = parent::save();

        if ($return && $this->row && $this->table)
        {
            $relation                            = $this->getObject('com:attachments.model.entity.relation');
            $relation->attachments_attachment_id = $this->id;
            $relation->table                     = $this->table;
            $relation->row                       = $this->row;

            if (!$relation->load()) {
                $relation->save();
            }
        }

        // Save the thumbnail if the attachment is an image
        if ($this->file->isImage())
        {
            $thumbnail         = $this->getObject('com:files.model.entity.thumbnail');
            $thumbnail->source = $this->file;

            if (!file_exists($this->thumbnail_fullpath))
            {
                $thumbnail->setThumbnailSize(4 / 3)
                    ->generateThumbnail()
                    ->save($this->thumbnail_fullpath);
            }

            if (isset($this->x1) && isset($this->x2))
            {
                // Cropping existing thumbnail
                $thumbnail->setProperties(array(
                    'source' => $this->file,
                    'x1'     => $this->x1,
                    'x2'     => $this->x2,
                    'y1'     => $this->y1,
                    'y2'     => $this->y2
                ))
                    ->cropThumbnail()
                    ->save($this->thumbnail_fullpath);
            }
        }

        return $return;
    }

    public function delete()
    {
        $return = parent::delete();

        if ($return)
        {
            try
            {
                $query = array(
                    'container' => $this->container,
                    'name'      => array(
                        $this->path,
                        $this->thumbnail
                    )
                );

                $controller = $this->getObject('com:files.controller.file');
                $controller->getRequest()->setQuery($query);

                $controller->delete();
            }
            catch (Library\ControllerExceptionResourceNotFound $e) {}

            $this->getObject('com:attachments.database.table.relations')
                ->select(array('attachments_attachment_id' => $this->id))
                ->delete();
        }

        return $return;
    }

    public function getPropertRelation()
    {
        $relation = $this->getObject('com:attachments.database.table.relations')
            ->select(array('attachments_attachment_id' => $this->id), Library\Database::FETCH_ROW);

        return $relation;
    }

    public function getPropertyFile()
    {
        $file = $this->getObject('com:files.model.files')
            ->container($this->container)
            ->name($this->path)
            ->fetch();

        return $file;
    }

    public function getPropertyThumbnail()
    {
        $path            = pathinfo($this->path);
        $path['dirname'] = $path['dirname'] === '.' ? '' : $path['dirname'] . '/';

        $thumbnail = $path['dirname'] . $path['filename'] . '_thumb.' . $path['extension'];

        return $thumbnail;
    }

    public function getPropertyThumbnailFullpath()
    {
        return dirname($this->file->fullpath) . '/' . $this->thumbnail;
    }

    public function toArray()
    {
        $data              = parent::toArray();
        $data['thumbnail'] = $this->thumbnail;

        return $data;
    }
}