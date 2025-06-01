<?php
class Donations extends Controller {
    private $donationService;

    public function __construct() {
        // $this->donationService = new TransbankDonationService();
    }

    public function index() {
        $this->view('donations');
    }

    public function create(){
        #Aca tenemos que mandar al usuario al donation handler
        #falta que el rellenado de datos venga del formulario de la pagina donations.view
        $donationHandler = new donation_handler;
        $result = $donationHandler->createDonation(1000000000, [
            'name' => 'Juan Pérez',
            'email' => 'juan@email.com',
            'phone' => '+56912345678'
        ]);

        //Redirigimos a la pagina de tbank para que ingrese la tarjeta y pague el usuario
        if ($result['success']) {
            // Redirigir al usuario a Transbank
            header('Location: ' . $result['url'] . '?token_ws=' . $result['token']);
        } else {
            echo 'Error: ' . $result['error'];
        }
    }
}