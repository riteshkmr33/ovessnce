<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino Socket 2.0                                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

/**
 *  Developed by: Reazaul Karim - Rubel
 *  URI: http://reazulk.wordpress.com
 *  Contact: reazulk@gmail.com
 *  Call: +8801717403818
 *  ---------------------------------------------------------------
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  ---------------------------------------------------------------
 */

class dbimpexp
{

    // Databse object
    private $link = NULL;

    // Download flag
    private $download = false;

    // Path and Extension
    private $download_path = "";
    private $file_name = NULL;
    private $file_ext = ".xml";
    private $import_path = "";

    /**
     * Constract dbimpexp constactor
     *
     */
    public function __construct() { }

    /**
     * Add Common values
     */ 
    public function addValue( $key = NULL ,$val = NULL )
    {
        if( !is_null( $key ) )
        {
            $this->$key = $val;
        }

        // Return Base referance
        return $this;
    }

    /**
     * Export data
     *
     * @return null
     */
    public function export()
    {
        
        $dom = new DOMDocument('1.0');
        $database_name = DB_NAME;
   		global $lsdb;

        // Create Database node
        $database = $dom->createElement('database');
        $database = $dom->appendChild($database);
        $database->setAttribute ('name', $database_name);

        //create schema node
        $schema = $dom->createElement('schema');
        $schema = $dom->appendChild($schema);
        
        /* ---- CREATE SCHEMA ---- */
        // Fetch table informaton 
        $tableQuery = $lsdb->query("SHOW TABLES");        

        while ($tableRow = $tableQuery->fetch_row())
        {
        	
        	$rowsc = $lsdb->query("SELECT * FROM {$tableRow[0]}");
        	
        	if ($lsdb->affected_rows > 0) {
        	
            //Table Node
            $table = $dom->createElement('table');
            $table = $dom->appendChild($table);
            $table->setAttribute('name', $tableRow[0]);
            
            //Fetch table description
            $fieldQuery = $lsdb->query("DESCRIBE $tableRow[0]");
            
            while ($fieldRow = $fieldQuery->fetch_assoc())
            {
                //Create Field node
                $field = $dom->createElement( 'field' );
                $field = $dom->appendChild( $field );
                $field->setAttribute('name', $fieldRow['Field']);
                $field->setAttribute('name', $fieldRow['Field']);
                $field->setAttribute('type', $fieldRow['Type']);
                $field->setAttribute('null', strtolower($fieldRow['Null']));
                
                //set the default
                if ($fieldRow['Default'] != '')
                {
                    $field->setAttribute('default', strtolower($fieldRow['Default']));
                }

                //set the key
                if ($fieldRow['Key'] != '')
                {
                    $field->setAttribute('key', strtolower($fieldRow['Key']));
                }

                //set the value/length attribute
                if ($fieldRow [ 'Extra' ] != '')
                {
                    $field->setAttribute ( 'extra', strtolower($fieldRow['Extra']));
                }
                
                //put the field inside of the table
                $table->appendChild($field);
            }
            
            //put the table inside of the schema
            $schema->appendChild($table);
            
            }
        }
        
        // Add Schema to database
        $database->appendChild($schema);
    
        
        /* ------- Populate Data ------ */
        $tableQuery = $lsdb->query("SHOW TABLES");
        
        // Create Data node
        $data = $dom->createElement ('data');
        $data = $dom->appendChild ($data);
        $dom->appendChild ($data);

        while ($tableRow = $tableQuery->fetch_row())
        {
        
        	$rowsc = $lsdb->query("SELECT * FROM {$tableRow[0]}");
        	
        	if ($lsdb->affected_rows > 0) {
        	
            // Read Table Schema again
            $descQuery = $lsdb->query("DESCRIBE {$tableRow[0]}");
            $schema = Array();
            while ($row = $descQuery->fetch_assoc())
            {
               $schema[$row['Field']] = array
                                        (
                                            "Type" =>$row['Type'],
                                            "Null" =>$row['Null'],
                                            "Key" =>$row['Key'],
                                            "Default" =>$row['Default'],
                                            "Extra" =>$row['Extra']
                                        );
            }

            $rows = $lsdb->query("SELECT * FROM {$tableRow[0]}");            
            $table = $dom->createElement ($tableRow[0]);
            $table = $dom->appendChild ($table);

            $data->appendChild ($table);

            while ($row = $rows->fetch_assoc())
            {
                //Create Row node
                $data_row = $dom->createElement('dbrow');
                $data_row = $dom->appendChild($data_row);
                $table ->appendChild($data_row);
                
                // Create Row Node
                foreach($row as $key => $val)
                {
                    if( strstr($schema[$key]['Type'], 'int') || strstr($schema[$key]['Type'], 'float') || strstr($schema[$key]['Type'], 'date') || strstr($schema[$key]['Type'], 'time'))
                    {
                        $field = $dom->createElement($key,$val);
                        $field = $dom->appendChild($field);
                        $data_row->appendChild($field);

                    }
                    else
                    {
                        $field = $dom->createElement($key);
                        $field = $dom->appendChild($field);
                        $data_row->appendChild($field);
                        $cdataNode = $dom->createCDATASection(utf8_encode($val));
                        $cdataNode = $dom->appendChild($cdataNode);
                        $field->appendChild($cdataNode);
                    }                  
                }
            }
            
           }
        }
        
        // Add Data to root node
        $database->appendChild($data);

        $database_name = (isset($this->file_name)) ? $this->file_name : $database_name;

        // Write XML
        $dom->formatOutput = true;
        $dom->saveXML();
        
        // Download file
        if ($this->download) {
        
            $filename =  time().$this->file_ext;
            
            // We sabotage the cache directory for temporaring saving the file! :)
            $xml = $dom->save(APP_PATH.LS_CACHE_DIRECTORY.'/'.$filename);

            header('Content-type: text/appdb');
            header('Content-Disposition: attachment; filename='.$database_name);
            readfile(APP_PATH.LS_CACHE_DIRECTORY.'/'.$filename);
            @unlink(APP_PATH.LS_CACHE_DIRECTORY.'/'.$filename);
            exit;
        }
    }

    /**
     * Import Databse
     *
     * @return null
     */
    public function import()
        {
        
        	global $lsdb;
          
            if ($this->import_path == "" || !file_exists($this->import_path))
            {
                die("Database file not exists");
            }    
            
            $dom = new DOMDocument();
            $dom->load($this->import_path);
    
            // Read Schema
            $schema = $dom->getElementsByTagName('schema');
            $tables = $schema->item(0)->getElementsByTagName('table');
            
    
            foreach ($tables as $table) {    	
                // Get Table Name
                $name = $table->getAttribute('name');
                $fields = $table->getElementsByTagName('field');
    
                // Get table data
                $dable_data = $dom->getElementsByTagName($name);
                $rows = $dable_data->item(0)->getElementsByTagName('dbrow');
    
                $sqlbody = '';
                foreach ($rows as $row) {
                
                    $tmp_body = '';
                    $tmp_head = '';
                    foreach ($fields as $field) {
                    
                        $field_name = $field->getAttribute('name');
                        $field_type = $field->getAttribute('type');
                        $entry = $row->getElementsByTagName($field_name);
                        $field_value = utf8_decode($entry->item(0)->nodeValue);
                        $field_value = $this->quote_smart($field_value);
    
                        $tmp_body .= ($tmp_body == '') ? $field_value : ",{$field_value}";
    
                        if ($tmp_body != '') $tmp_head .= ($tmp_head == "") ? "`{$field_name}`" : ",`{$field_name}`";
                    }
                   
                     $sqlbody .=  ($sqlbody == '') ?  "($tmp_body)\n" :  ",($tmp_body)\n";
                }
                
                if ($sqlbody) {
                
	                $lsdb->query("TRUNCATE TABLE `{$name}` ");
	                $lsdb->query("INSERT INTO `{$name}` ({$tmp_head}) VALUES {$sqlbody}");
	                
	            }
	            
            }
        }
    
    
    public function optimize()
    {
    	global $lsdb;
    	$result = $lsdb->query('SHOW TABLES');
     	while($table = $result->fetch_assoc())
    	{
    	
    		foreach ($table as $db => $tablename) 
    		   { 
    		       $lsdb->query('OPTIMIZE TABLE '.$tablename); 
    		   }
    	}
    }

    public function quote_smart($value)
	{
		global $lsdb;
		
		$value = smartsql($value);
		
		// Quote if not integer
		if (!is_numeric($value)) $value = "'".$value."'";
		
		return $value;
	}
}