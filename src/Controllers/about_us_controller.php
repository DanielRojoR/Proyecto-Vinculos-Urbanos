<?php
require_once __DIR__ . '/../Models/about_us_model.php';

class AboutUsController {
    public function index() {
        $model = new AboutUsModel();
        $aboutData = $model->getAboutData();
        include __DIR__ . '/../Views/about_us_view.php';
    }
}
?>
