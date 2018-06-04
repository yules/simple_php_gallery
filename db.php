<?php
/**
 * created by: Yuval Leshem
 * yuval.leshem@gmail.com
 * http://simplegalleryma.sourceforge.net/
**/
include 'config.php';

$imgtable = 'images';
$gallerytable = 'galleries';
$userstable = 'users';

function init_db() 
{	
	$database = $GLOBALS[database];

if (!mysql_connect($GLOBALS[db_host], $GLOBALS[db_user], $GLOBALS[db_pwd]))
	die("Can't connect to database" . mysql_error());

if (!mysql_select_db($database))
	die("Can't select database" . mysql_error());

}

// prevent sql injection
function sql_safe($s)
{
	if (get_magic_quotes_gpc())
	$s = stripslashes($s);

	return mysql_real_escape_string($s);
}

// dosql - perform insert/ update/ delete query (no result needed)
function dosql($sql) {
    $result = mysql_query($sql); 
    
	if (!$result) {
		die('invalid sql, error [' . mysql_error() . ']\nquery [' . $sql . ']');
	}
	return $result;
}

// get sql results. 
function getsql($sql) { 
    $ret = array(); 
    $result = dosql($sql);    
	
    $i = 0; 
    while ($i < mysql_num_fields($result)) {            
        $fields[] = mysql_fetch_field($result, $i); 
        $i++; 
    } 
    
    while ($row = mysql_fetch_row($result)) {                
        $new_row=array(); 
        for($i=0; $i < count($row); $i++) { 
            $new_row[$fields[$i]->name] = htmlentities($row[$i], ENT_QUOTES, "UTF-8"); 
        } 
        $ret[] =$new_row; 
    } 
    
    return $ret; 
}      
?>