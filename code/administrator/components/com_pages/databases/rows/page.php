<?php
/**
 * @version     $Id: page.php 3035 2011-10-09 16:57:12Z johanjanssens $
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Page Database Row Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Pages
 */

class ComPagesDatabaseRowPage extends KDatabaseRowTable implements KServiceInstantiatable
{
    protected $_page_xml;

    protected $_state;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if($config->state) {
            $this->_state = $config->state;
        }
    }

    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        $type = 'Page';

        if($config->state && $config->state->type) {
            $type = ucfirst($config->state->type['name']);
        } elseif($config->data && $config->data->type) {
            $type = ucfirst($config->data->type);
        }

        //Override the identifier
        $container->get('koowa:loader')->loadIdentifier('com://admin/pages.database.row.'.strtolower($type));
        $config->service_identifier = $container->getIdentifier('com://admin/pages.database.row.'.strtolower($type));

        //Create the page object
        $classname = 'ComPagesDatabaseRow'.$type;
        $instance  = new $classname($config);

        return $instance;
    }

    public function save()
    {
        // Set home.
        if($this->isModified('home') && $this->home == 1)
        {
            $page = $this->getService('com://admin/pages.database.table.pages')
                ->select(array('home' => 1), KDatabase::FETCH_ROW);

            $page->home = 0;
            $page->save();
        }
        
        // Update child pages if menu has been changed.
        if(!$this->isNew() && $this->isModified('pages_menu_id'))
        {
            $descendants = $this->getDescendants();
            if(count($descendants)) {
                $descendants>setData(array('pages_menu_id' => $this->pages_menu_id))->save();
            }
        }

        return parent::save();
    }

    /**
     * Returns the siblings of the row
     *
     * @return KDatabaseRowAbstract
     */
    public function getSiblings()
    {
        if($this->id)
        {
            $table = $this->getTable();
            $query = $this->getService('koowa:database.query.select')
                ->where('tbl.'.$table->getIdentityColumn().' <> :id')
                ->where('tbl.pages_menu_id = :pages_menu_id')
                ->having('level = :level')
                ->bind(array(
                    'id' => $this->id,
                    'pages_menu_id' => $this->pages_menu_id,
                    'level' => $this->level));

            $parent_ids = $this->getParentIds();
            if($parent_ids)
            {
                $query->join(array('closures' => $table->getRelationTable()), 'closures.descendant_id = tbl.'.$table->getIdentityColumn(), 'INNER')
                    ->where('closures.ancestor_id = :parent_id')
                    ->bind(array('parent_id' => $this->parent_id));
            }

            $result = $this->getTable()->select($query, KDatabase::FETCH_ROWSET);
        }
        else $result = null;

        return $result;
    }

    protected function _getPageXml()
    {
        if(!isset($this->_page_xml))
        {
            $xml  = JFactory::getXMLParser('simple');
            $path = dirname($this->getIdentifier()->filepath).'/'.$this->getIdentifier()->name.'.xml';

            if(file_exists($path)) {
                $xml->loadFile($path);
            }

            $this->_page_xml = $xml;
        }

        return $this->_page_xml;
    }

    public function __get($name)
    {
        switch($name)
        {
            case 'params_advanced':
            {
                if(!isset($this->_data['params_advanced']))
                {
                    $params = new JParameter($this->params);
                    $state  = $this->_getPageXml()->document->getElementByPath('state');

                    if($state instanceof JSimpleXMLElement) {
                        $params->setXML($state->getElementByPath('advanced'));
                    }

                    $this->_data['params_advanced'] = $params;
                }

            } break;

            case 'params_state':
            {
                if(!isset($this->_data['params_state']))
                {
                    $params = new JParameter($this->params);
                    $state  = $this->_getPageXml()->document->getElementByPath('state');

                    if($state instanceof JSimpleXMLElement) {
                        $params->setXML($state->getElementByPath('params'));
                    }

                    $this->_data['params_state'] = $params;
                }

            } break;
        }

        return parent::__get($name);
    }
}
