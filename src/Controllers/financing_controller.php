<?php

namespace Controllers;


class FinancingController
{
    // Muestra la lista de financiamientos
    public function index()
    {
        $financings = Financing::all();
        View::render('financing/index', ['financings' => $financings]);
    }

    // Muestra el formulario para crear un nuevo financiamiento
    public function create()
    {
        View::render('financing/create');
    }

    // Guarda un nuevo financiamiento
    public function store()
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'amount' => $_POST['amount'] ?? 0,
            'description' => $_POST['description'] ?? ''
        ];

        $financing = new Financing($data);
        if ($financing->save()) {
            header('Location: /financing');
            exit;
        } else {
            View::render('financing/create', ['error' => 'Error al guardar']);
        }
    }

    // Muestra el formulario para editar un financiamiento existente
    public function edit($id)
    {
        $financing = Financing::find($id);
        if (!$financing) {
            header('Location: /financing');
            exit;
        }
        View::render('financing/edit', ['financing' => $financing]);
    }

    // Actualiza un financiamiento existente
    public function update($id)
    {
        $financing = Financing::find($id);
        if (!$financing) {
            header('Location: /financing');
            exit;
        }

        $financing->name = $_POST['name'] ?? $financing->name;
        $financing->amount = $_POST['amount'] ?? $financing->amount;
        $financing->description = $_POST['description'] ?? $financing->description;

        if ($financing->save()) {
            header('Location: /financing');
            exit;
        } else {
            View::render('financing/edit', ['financing' => $financing, 'error' => 'Error al actualizar']);
        }
    }

    // Elimina un financiamiento
    public function delete($id)
    {
        $financing = Financing::find($id);
        if ($financing) {
            $financing->delete();
        }
        header('Location: /financing');
        exit;
    }
}
