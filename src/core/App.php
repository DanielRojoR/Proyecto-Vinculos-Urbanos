<?php
class App {

    private $controller = 'Home';
    private $method = 'index';

    function __construct(){
        $this->loadController();
    }

    private function splitURL(){

        #inicializa la variable URL con lo que haya en la url guardada desde el enrutador de htaccess, si no encuentra nada, manda a la ruta ´home' por defecto, posiblemente 
        #haya que cambiar esto a futuro para que redireccione por defecto al homepage, ya que el index ahora solo es un hub global de redireccionamiento
        $URL = $_GET['url'] ?? 'home';
        # Crea una variable URL para ejecutar explode (separar palabras en una cadena de texto)
        # El separador en este caso es '/' y la cadena de texto la sacamos de la superglobal $_GET['url']
        # la variable $_GET['url'] la sacamos del htaccess, que guarda en 'url' la direccion que el usuario haya escrito en el navegador
        $URL = explode("/",trim($URL,"/"));
        return $URL;
    }

    private function loadController(){

        #la url se saca del metodo $_GET proporcionado por el servidor apache, este contiene las
        #url o rutas que el usuario ingresa en el navegador, o que redireccionemos nosotros
        $URL = $this->splitURL();

        $filename = "../src/controllers/".ucfirst($URL[0]).".php";
        
        #Selecciona el controlador basandose en el segmento de url
        if(file_exists($filename)){
            require $filename;
            $this->controller = ucfirst($URL[0]);
        } else {

            $filename = "../src/controllers/_404.php";
            require $filename;
            $this->controller = '_404';
        }

        $controller = new $this->controller; 

        #Selecciona a que metodo del controlador accede
        if(!empty($URL[1])){
            
            if(method_exists($controller,$URL[1])){
                $this->method = $URL[1];
                echo($URL[1]);
            }
        }

        call_user_func_array([$controller,$this->method],[]);
    }
}