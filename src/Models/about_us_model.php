<?php
class AboutUsModel {
    public function getAboutData() {
        return [
            'historia' => 'Cinco personas, amigos desde los 15 años, participaron en el voluntariado “Cruzando Fronteras”, activo desde 1998 en el barrio Sol Poniente de Maipú.',
            'objetivo' => 'Formar una organización formal y estructurada para prevenir el consumo de sustancias en niños, niñas y adolescentes de sectores vulnerables, generando un impacto social a través de la intervención temprana, la educación y el trabajo comunitario.',
            'equipo' => [
                ['nombre' => 'Camila V.', 'rol' => 'Diseñadora UI/UX', 'imagen' => 'https://www.placehold.co/500x500'],
                ['nombre' => 'Daniel R.', 'rol' => 'Full Stack Dev', 'imagen' => 'https://www.placehold.co/500x500'],
                ['nombre' => 'Samuel C.', 'rol' => 'Community Manager', 'imagen' => 'https://www.placehold.co/500x500']
            ],
            'director' => [
                'nombre' => 'Joaquín C.',
                'rol' => 'CEO',
                'imagen' => 'https://www.placehold.co/500x500'
            ]
        ];
    }
}
?>
