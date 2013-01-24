<?php
/**
 * @version		$Id$
 * @package     Nooku_Server
 * @subpackage  Application
 * @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Page Controller Class
 *   
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Application
 */
 
class ComApplicationControllerPage extends ComDefaultControllerView
{
    protected function _actionRender(KCommandContext $context)
    {
        //Assign variables to the view
        $this->getView()->content   = $context->response->getContent();

        $content = parent::_actionRender($context);

        //Make images paths absolute
        $path = KRequest::root()->getPath().'/'.str_replace(JPATH_ROOT.DS, '', JPATH_IMAGES.'/');
        $site = $this->getService('application')->getSite();

        $content = str_replace(JURI::base().'images/', $path, $content);
        $content = str_replace(array('"images/','"/images/', '"sites/'.$site.'/images') , '"'.$path, $content);

        $context->response->setContent($content);

        return $content;
    }
}