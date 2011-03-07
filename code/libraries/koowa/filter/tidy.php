<?php
/**
* @version		$Id$
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Tidy filter.
 * 
 * This filter will correct and escape a HTML fragment. It will also cleanup HTML
 * generated by Microsoft Office products. 
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Filter
 * @see         http://tidy.sourceforge.net/docs/quickref.html
 */
class KFilterTidy extends KFilterAbstract
{
    /**
     * A tidy object
     * 
     * @var object
     */
    protected $_tidy = null;

    /**
     * The input/output encoding
     * 
     * @var string
     */
    protected $_encoding = 'utf8';
    
    /**
     * @var array
     */
    protected $_config = array(
            'clean'                       => true,
            'drop-proprietary-attributes' => true, 
            'output-html'                 => true,
            'show-body-only'              => true,
            'bare'                        => true, 
            'wrap'                        => 0,
            'word-2000'                   => true
    );

    /**
     * Constructor
     *
     * @@param  object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        if(isset($config->diagnose)) {
            $this->_diagnose = $config->diagnose;
        }
        
        if(isset($config->encoding)) {
            $this->_encdoing = $config->encoding;
        }
        
        if(isset($config->config)) {
            //$this->_config = array_merge($this->_config, $config['config']);
        }
    }
    
    /**
     * Validate a variable
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value));
    }
    
    /**
     * Sanitize a variable
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {   
        //Tidy is not installed, return the input
        if($tidy = $this->getTidy($value)) {
            $value = $tidy->cleanRepair();  
        }
        
        return $value; 
    }
    
    /**
     * Gets a Tidy object
     * 
     * @param string    The data to be parsed.  
     */
    public function getTidy($string)
    {
        if(class_exists('Tidy')) 
        {
            if (!$this->_tidy) {
                $this->_tidy = new Tidy();
            }
           
            $this->_tidy->parseString($string, $this->_config, $this->_encoding);
        }
        
        return $this->_tidy;
    }
    
}