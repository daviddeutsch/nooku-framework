<?php
class ComLanguagesDatabaseBehaviorTranslatable extends KDatabaseBehaviorAbstract
{
    protected function _beforeTableSelect(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage') && $context->query)
        {
            $languages = $application->getLanguages();
            $active    = $languages->getActive();
            $primary   = $languages->getPrimary();
            
            // Modify table in the query if active language is not the primary.
            if($active->iso_code != $primary->iso_code)
            {
                $query  = $context->query;
                $tables = $this->getService('com://admin/languages.model.tables')->enabled(true)->getList();
                
                $table = current($query->table);
                if(is_string($table) && in_array($table, $tables->name)) {
                    $context->query->table[key($query->table)] = strtolower($active->iso_code).'_'.$table;
                }
            }
        }
    }
    
    protected function _afterTableInsert(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage') && $context->affected)
        {
            $tables = $this->getService('com://admin/languages.model.tables')->enabled(true)->getList();
            
            // Check if table is translatable.
            if(in_array($context->table, $tables->name))
            {
                $languages = $application->getLanguages();
                $active    = $languages->getActive();
                $primary   = $languages->getPrimary();
                
                $item = array(
                    'iso_code'   => $active->iso_code,
                    'table'      => $context->table,
                    'row'        => $context->data->id,
                    'title'      => $context->data->title,
                    'created_on' => $context->data->created_on,
                    'created_by' => $context->data->created_by,
                    'status'     => ComLanguagesDatabaseRowItem::STATUS_COMPLETED,
                    'original'   => 1
                );
                
                // Insert item into the items table.
                $this->getService('com://admin/languages.database.row.item')
                    ->setData($item)
                    ->save();
                
                // Insert item into language specific tables.
                $table = $tables->find(array('name' => $context->table))->top();
                
                foreach($languages as $language)
                {
                    if($language->iso_code != $primary->iso_code)
                    {
                        $query = clone $context->query;
                        $query->table = strtolower($language->iso_code).'_'.$query->table;
                        
                        if(($key = array_search($table->unique_column, $query->columns)) !== false) {
                            $query->values[0][$key] = $context->data->id;
                        }
                        
                        $this->getTable()->getDatabase()->insert($query);
                    }
                    
                    if($language->iso_code != $active->iso_code)
                    {
                        // Insert item into items table.
                        $item['iso_code'] = $language->iso_code;
                        $item['status'] = ComLanguagesDatabaseRowItem::STATUS_MISSING;
                        $item['original'] = 0;
                        
                        $this->getService('com://admin/languages.database.row.item')
                            ->setData($item)
                            ->save();
                    }
                }
            }
        }
    }
    
    protected function _beforeTableUpdate(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage'))
        {
            // Modify table in the query if translatable.
            $tables = $this->getService('com://admin/languages.model.tables')->enabled(true)->getList();
            
            if(in_array($context->table, $tables->name))
            {
                $languages = $application->getLanguages();
                $active    = $languages->getActive();
                $primary   = $languages->getPrimary();
                
                if($active->iso_code != $primary->iso_code) {
                    $context->query->table = strtolower($active->iso_code).'_'.$context->query->table;
                }
            }
        }
    }
    
    protected function _afterTableUpdate(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage') && $context->data->getStatus() == KDatabase::STATUS_UPDATED)
        {
            $tables = $this->getService('com://admin/languages.model.tables')->enabled(true)->getList();
            
            if(in_array($context->table, $tables->name))
            {
                $languages = $application->getLanguages();
                $primary   = $languages->getPrimary();
                $active    = $languages->getActive();
                
                // Update item in the items table.
                $table = $tables->find(array('name' => $context->table))->top();
                $item  = $this->getService('com://admin/languages.database.table.items')
                    ->select(array(
                        'iso_code' => $active->iso_code,
                        'table'    => $context->table,
                        'row'      => $context->data->id
                    ), KDatabase::FETCH_ROW);
                
                $item->setData(array(
                    'title'  => $context->data->{$table->title_column},
                    'status' => ComLanguagesDatabaseRowItem::STATUS_COMPLETED
                ))->save();
                
                // Set the other items to outdated if they were completed before.
                $query = $this->getService('koowa:database.query.select')
                    ->where('iso_code <> :iso_code')
                    ->where('table = :table')
                    ->where('row = :row')
                    ->where('status = :status')
                    ->bind(array(
                        'iso_code' => $active->iso_code,
                        'table' => $context->table,
                        'row' => $context->data->id,
                        'status' => ComLanguagesDatabaseRowItem::STATUS_COMPLETED
                    ));
                
                $items = $this->getService('com://admin/languages.database.table.items')
                    ->select($query);
                
                $items->status = ComLanguagesDatabaseRowItem::STATUS_OUTDATED;
                $items->save();
                
                // Copy the item's data to all missing items.
                $database = $this->getTable()->getDatabase();
                $prefix = $active->iso_code != $primary->iso_code ? strtolower($active->iso_code.'_') : '';
                $select = $this->getService('koowa:database.query.select')
                    ->table($prefix.$table->name)
                    ->where($table->unique_column.' = :unique')
                    ->bind(array('unique' => $context->data->id));
                
                $query->bind(array('status' => ComLanguagesDatabaseRowItem::STATUS_MISSING));
                $items = $this->getService('com://admin/languages.database.table.items')
                    ->select($query);
                
                foreach($items as $item)
                {
                    $prefix = $database->getTablePrefix().($item->iso_code != $primary->iso_code ? strtolower($item->iso_code.'_') : '');
                    $query = 'REPLACE INTO '.$database->quoteIdentifier($prefix.$table->name).' '.$select;
                    $database->execute($query);
                    
                    $item->setData(array(
                        'title' => $context->data->{$table->title_column},
                        'modified_by' => $context->data->modified_by,
                        'modified_on' => $context->data->modified_on
                    ))->save();
                }
            }
        }
    }
    
    protected function _beforeTableDelete(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage'))
        {
            // Modify table in the query if active language is not the primary.
            $tables = $this->getService('com://admin/languages.model.tables')->enabled(true)->getList();
            
            if(in_array($context->table, $tables->name))
            {
                $languages = $application->getLanguages();
                $active    = $languages->getActive();
                $primary   = $languages->getPrimary();
                
                if($active->iso_code != $primary->iso_code) {
                    $context->query->table = strtolower($active->iso_code).'_'.$context->query->table;
                }
            }
        }
    }
    
    protected function _afterTableDelete(KCommandContext $context)
    {
        $application = JFactory::getApplication();
        if($application->getCfg('multilanguage') && $context->data->getStatus() == KDatabase::STATUS_DELETED)
        {
            $languages = $application->getLanguages();
            $primary   = $languages->getPrimary();
            $active    = $languages->getActive();
            
            // Remove item from other tables too.
            $database = $this->getTable()->getDatabase();
            $query    = clone $context->query;
            
            foreach($languages as $language)
            {
                if($language->iso_code != $active->iso_code)
                {
                    $prefix = $language->iso_code != $primary->iso_code ? strtolower($language->iso_code.'_') : ''; 
                    $query->table = $prefix.$context->table;
                    $database->delete($query);
                }
            }
            
            // Mark item as deleted in items table.
            $this->getService('com://admin/languages.database.table.items')
                ->select(array('table' => $context->table, 'row' => $context->data->id))
                ->setData(array('deleted' => 1))
                ->save(); 
        }
    }
}