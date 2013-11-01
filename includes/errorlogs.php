<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function log_action($string){

//if (is_writable('logs')) {
$textfile = 'includes/logs/logfile-'.date('MY').'.log';
//$textfile = 'logs/logfile.txt';
$tab = "\t"; $newline = "\r";

if (!$handle = fopen($textfile, 'a')) 
        die('Cannot open file: '.$textfile);

if (fwrite($handle, "[".date('j M Y h:i:s a')."]".utf8_encode($tab).
                        $string.utf8_encode($newline)
            ) === false)
       die('Cannot write to file: '.$textfile);

fclose($handle);

}

function log_error($string){
    
$textfile = 'includes/logs/logfile-'.date('MY').'.log';
//$textfile = 'logs/logfile.txt';
$tab = "\t"; $newline = "\r";

if (!$handle = fopen($textfile, 'a')) 
        die('Cannot open file: '.$textfile);

if (fwrite($handle, "[".date('j M Y h:i:s a')."]".utf8_encode($tab).
                        $string.utf8_encode($newline)
            ) === false)
       die('Cannot write to file: '.$textfile);

fclose($handle);

}

?>