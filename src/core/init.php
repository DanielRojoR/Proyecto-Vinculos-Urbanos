<?php

spl_autoload_register(function($classname){
    $filename = "../src/models/".ucfirst($classname).".php";
    if(file_exists($filename)){
        require $filename;
    }
});
    
require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';