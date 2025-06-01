<?php

class Controller {

    #esta funcion es heredada por los controladores 
<<<<<<< HEAD
    public function view($name, $data = []){
        $filename = "../src/views/".$name.".view.php";
        if(file_exists($filename)){
            // Convertir el array de datos en variables
            extract($data);
=======
    public function view($name){
        $filename = "../src/views/".$name.".view.php";
        if(file_exists($filename)){
>>>>>>> 1211b5ed73fbc7d2cb17508f7b860b812c5f6a6a
            require $filename;
        } else {
            $filename = "../views/404.view.php";
            require $filename;
        }
    }
<<<<<<< HEAD
}

// class Controller {
//     protected function view($view, $data = []) {
//         // Convert object to array if necessary
//         if (is_object($data)) {
//             $data = (array) $data;
//         }
        
//         // Only attempt to extract if data is an array
//         if (is_array($data)) {
//             extract($data);
//         }
        
//         // Include the view file
//         if (file_exists("../src/views/" . $view . ".view.php")) {
//             require "../src/views/" . $view . ".view.php";
//         }
//     }
// }
=======
}
>>>>>>> 1211b5ed73fbc7d2cb17508f7b860b812c5f6a6a
