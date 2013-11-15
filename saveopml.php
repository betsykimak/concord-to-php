<?php

// enable error reporting
ini_set('display_errors',true);
error_reporting(E_ALL);
var_dump($_REQUEST);

// Viewing this page directy will give an undefined index error

$myArray = $_POST['testData'];
$name = $_POST['name'];

//$filename = 'ajaxout.txt'; // use to overwrite a single file
$filename = 'concord-' . $name . '.opml'; // use to export one opml file per title
$somecontent = $myArray;

    //check if file exists
    if (file_exists($filename)) {
        // if exists overwrite file
        $fh = fopen($filename, 'w');
        fwrite($fh, $somecontent);
    } else {
        // if does not exist, write new file
        $fh = fopen($filename, 'w');
        fwrite($fh, $somecontent);
    }
?>