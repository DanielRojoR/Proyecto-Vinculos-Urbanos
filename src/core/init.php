<?php

<<<<<<< HEAD
spl_autoload_register(function($classname){
    $filename = "../src/models/".ucfirst($classname).".php";
    if(file_exists($filename)){
        require $filename;
    }
});
    
=======
>>>>>>> 1211b5ed73fbc7d2cb17508f7b860b812c5f6a6a
require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';