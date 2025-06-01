<?php
class Home extends Controller{
    public function index(){

        $data = [];
        $home = new Home();
        $this->view('home',$data);        
    }
}
