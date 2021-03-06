<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Activities;

use Nooku\Library;

/**
 * Loggable Database Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Activities
 */
class DatabaseBehaviorLoggable extends Library\DatabaseBehaviorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  Library\ObjectConfig $config A ObjectConfig object with configuration options
     *
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'auto_mixin' => true
        ));

        parent::_initialize($config);
    }

    /**
     * Get a list of activities
     *
     * @return Library\ModelEntityInterface
     */
    public function getActivities()
    {
        $activities = $this->getObject('com:activities.model.activities')
            ->row($this->id)
            ->package($this->getTable()->getIdentifier()->package)
            ->name($this->getTable()->getName())
            ->fetch();

        return $activities;
    }
}