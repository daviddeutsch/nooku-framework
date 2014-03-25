<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Revisions;

use Nooku\Library;

/**
 * Revision Model Entity
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Revisions
 */
class ModelEntityRevision extends Library\ModelEntityRow
{
	public function getProperty($name)
    {
    	if($name == 'data' && is_string($this->_data['data'])) {
			$this->_data['data'] = json_decode($this->_data['data'], true);
		}

    	return parent::getProperty($name);
    }

    public function setStatus($status)
    {
        if($status == 'trashed') {
            parent::setStatus(self::STATUS_DELETED);
        }

        $this->_status = $status;
        return $this;
    }
}