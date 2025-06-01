<?php
class AboutUs extends Controller{
    public function index(){

        $data = [];
        $home = new Home();
        $this->view('aboutUs',$data);        
    }
}