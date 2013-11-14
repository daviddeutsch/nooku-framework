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
 * Files Html View
 *
 * @author  Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package Nooku\Component\Files
 */

class ViewFilesHtml extends Library\ViewHtml
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->auto_assign = false;

		parent::_initialize($config);
	}

    public function setData(Library\ObjectConfigInterface $data)
	{
	    $state = $this->getModel()->getState();

        //Set the limit
        if (empty($state->limit)) {
	        $state->limit = $this->getObject('application')->getCfg('list_limit');
	    }

        $data->container = $this->getModel()->getContainer();
        $data->site      = $this->getObject('application')->getSite();
		$data->token     = $this->getObject('user')->getSession()->getToken();

		return parent::setData($data);
	}
}
