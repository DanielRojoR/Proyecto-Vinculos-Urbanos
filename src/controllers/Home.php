<?php
<<<<<<< HEAD
class Home extends Controller{
    public function index(){

        $data = [];
        $home = new Home();
        $this->view('home',$data);        
=======

class Home extends Controller{
    public function index(){
        $this->view('home');        
>>>>>>> 1211b5ed73fbc7d2cb17508f7b860b812c5f6a6a
    }
}
