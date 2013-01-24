<?php
/**
 * @version     $Id: html.php 4840 2012-08-23 22:49:59Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Module View
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Modules
 * @subpackage  Default
 */
class ComDefaultModuleDefaultHtml extends KViewHtml
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'model'      => 'com://admin/default.model.module',
            'media_url'  => KRequest::root() . '/media',
            'data'	     => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Renders and echo's the views output
     *
     * @return ModDefaultHtml
     */
    public function display()
    {
        JFactory::getLanguage()->load($this->getIdentifier()->package, $this->module->name);

        //Dynamically attach the chrome filter
        if(!empty($this->module->chrome)) {
            $this->getTemplate()->attachFilter('chrome', array('styles' => $this->module->chrome));
        }

        if(empty($this->module->content))
        {
            $identifier = clone $this->getIdentifier();
            $identifier->name = $this->getLayout();

            $this->output = $this->getTemplate()
                ->loadIdentifier($identifier, $this->_data)
                ->render();
        }
        else
        {
            $this->output = $this->getTemplate()
                ->loadString($this->module->content, $this->_data, false)
                ->render();
        }

        return $this->output;
    }
}