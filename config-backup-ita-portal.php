<?php  /// Moodle Configuration File 


unset($CFG);

$CFG->dbtype    = 'mysql';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'portal';
$CFG->dbpass    = 'k4se*prt4l';
$CFG->dbpersist =  false;
$CFG->prefix    = 'mdl_';

// $CFG->wwwroot   = 'https://ita-portal.cis.fiu.edu';
$CFG->wwwroot   = 'http://ita-portal.cis.fiu.edu';
// $CFG->wwwroot   = '';
//$CFG->wwwroot   = 'http://64.77.83.36/moodle';
$CFG->dirroot   = '/var/www/html/moodle';
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

require_once("$CFG->dirroot/lib/setup.php");
// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>
