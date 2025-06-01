<?php

if ($_SERVER['SERVER_NAME'] == 'localhost'){
    define('ROOT','http://localhost/public');
} else {
    #esto hay que cambiarlo cuando hosteemos la pagina en un server real, ya que tiene que ser la direccion del server para poder tener
    #la direccion correcta del root
    define('ROOT','https//:www.sitioweb.cl');
}