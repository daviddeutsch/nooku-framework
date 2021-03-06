<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * File Size Filter
 *
 * @author  Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package Nooku\Component\Files
 */
class FilterFileSize extends Library\FilterAbstract
{
	public function validate($entity)
	{
		$max = $entity->getContainer()->getParameters()->maximum_size;

		if ($max)
		{
			$size = $entity->contents ? strlen($entity->contents) : false;

			if (!$size && is_uploaded_file($entity->file)) {
				$size = filesize($entity->file);
			} elseif ($entity->file instanceof \SplFileInfo && $entity->file->isFile()) {
				$size = $entity->file->getSize();
			}

			if ($size && $size > $max) {
				return $this->_error(\JText::_('File is too big'));
			}
		}
	}
}
