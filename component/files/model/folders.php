<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * Folders Model
 *
 * @author  Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package Nooku\Component\Files
 */
class ModelFolders extends ModelNodes
{
	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

		$this->_state->insert('tree', 'boolean', false);
	}

	public function getRowset()
	{
		if (!isset($this->_rowset))
		{
			$state = $this->_state;

			$folders = $state->container->getAdapter('iterator')->getFolders(array(
				'path'    => $this->_getPath(),
				'recurse' => !!$state->tree,
				'filter'  => array($this, 'iteratorFilter'),
				'map'     => array($this, 'iteratorMap'),
            	'sort'    => $state->sort
			));

        	if ($folders === false) {
        		throw new \UnexpectedValueException('Invalid folder');
        	}

			$this->_total = count($folders);

			if (strtolower($this->_state->direction) == 'desc') {
				$folders = array_reverse($folders);
			}

			$folders = array_slice($folders, $state->offset, $state->limit ? $state->limit : $this->_total);

			$results = array();
			foreach ($folders as $folder)
			{
				$hierarchy = array();
				if ($state->tree)
				{
					$hierarchy = explode('/', dirname($folder));
					if (count($hierarchy) === 1 && $hierarchy[0] === '.') {
						$hierarchy = array();
					}
				}

				$results[] = array(
					'container' => $state->container,
					'folder' 	=> $hierarchy ? implode('/', $hierarchy) : $state->folder,
					'name' 		=> basename($folder),
					'hierarchy' => $hierarchy
				);
			}

			$this->_rowset = $this->createRowset()->addRow($results);
		}

		return parent::getRowset();
	}

	public function iteratorMap($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = str_replace($this->_state->container->path.'/', '', $path);

		return $path;
	}

	public function iteratorFilter($path)
	{
		$filename = basename($path);
		if ($this->_state->name)
		{
			if (!in_array($filename, (array) $this->_state->name)) {
				return false;
			}
		}

		if ($this->_state->search && stripos($filename, $this->_state->search) === false) {
			return false;
		}
	}
}