<?php

class Controller {

    public function view($name){
        $filename = "../Views".$name.".View.php";
        if(file_exists($filename)){
            require $filename;
        } else {
            $filename = "../Views/404.view.php";
            require $filename;
        }
    }
}