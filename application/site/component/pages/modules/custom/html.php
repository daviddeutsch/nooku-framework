<?php
/**
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Custom Module Html View Class
 *
 * @author      Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @package     Nooku_Server
 * @subpackage  Pages
 */
 
class PagesModuleCustomHtml extends PagesModuleDefaultHtml
{
    public function render()
    {        
        $this->show_title = $this->module->params->get('show_title', false);
        
        return parent::render();
    }
} 