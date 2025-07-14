<?php
// dummy string
$string = "Hello, World!";

// save to text file
file_put_contents('output.txt', $string);

// read from text file
$readString = file_get_contents('output.txt');

// display the read string
echo $readString;

// end of script
?>