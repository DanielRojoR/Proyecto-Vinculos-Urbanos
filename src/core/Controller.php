<?php

class Controller {

    #esta funcion es heredada por los controladores 
    public function view($name){
        $filename = "../src/views/".$name.".view.php";
        if(file_exists($filename)){
            require $filename;
        } else {
            $filename = "../views/404.view.php";
            require $filename;
        }
    }
}