<?php

namespace Controllers;


class InitiativesController
{
    // Muestra la lista de iniciativas
    public function index()
    {
        $initiatives = Initiative::all();
        View::render('initiatives/index', ['initiatives' => $initiatives]);
    }

    // Muestra el formulario para crear una nueva iniciativa
    public function create()
    {
        View::render('initiatives/create');
    }

    // Guarda una nueva iniciativa
    public function store()
    {
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'author' => $_POST['author'] ?? '',
        ];

        $initiative = new Initiative($data);
        if ($initiative->save()) {
            header('Location: /initiatives');
            exit;
        } else {
            View::render('initiatives/create', ['errors' => $initiative->getErrors()]);
        }
    }

    // Muestra una iniciativa específica
    public function show($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative) {
            View::render('initiatives/show', ['initiative' => $initiative]);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "Iniciativa no encontrada.";
        }
    }

    // Muestra el formulario para editar una iniciativa
    public function edit($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative) {
            View::render('initiatives/edit', ['initiative' => $initiative]);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "Iniciativa no encontrada.";
        }
    }

    // Actualiza una iniciativa existente
    public function update($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative) {
            $initiative->title = $_POST['title'] ?? $initiative->title;
            $initiative->description = $_POST['description'] ?? $initiative->description;
            $initiative->author = $_POST['author'] ?? $initiative->author;

            if ($initiative->save()) {
                header('Location: /initiatives/' . $id);
                exit;
            } else {
                View::render('initiatives/edit', [
                    'initiative' => $initiative,
                    'errors' => $initiative->getErrors()
                ]);
            }
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "Iniciativa no encontrada.";
        }
    }

    // Elimina una iniciativa
    public function destroy($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative) {
            $initiative->delete();
            header('Location: /initiatives');
            exit;
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "Iniciativa no encontrada.";
        }
    }
}
?>