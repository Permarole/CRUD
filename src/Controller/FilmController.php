<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Model\FilmManager;
use App\Model\CategoryManager;

class FilmController extends AbstractController
{

    private FilmManager $filmManager;
    private CategoryManager $categoryManager;

    public function __construct()
    {
        parent::__construct();
        $this->filmManager = new FilmManager();
        $this->categoryManager = new CategoryManager();
    }

    public function index(): string
    {
        $films = $this->filmManager->selectAll();

        return $this->twig->render('Film/index.html.twig', ['films' => $films]);
    }


    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $film = $this->filmManager->selectOneById($id);
        $category = $this->categoryManager->selectOneById(($film['category_id']));

        return $this->twig->render('Film/show.html.twig', ['film' => $film, 'category' => $category]);
    }


    /**
     * Edit a specific item
     */
    public function edit(int $id): string
    {
        $film = $this->filmManager->selectOneById($id);
        $categories = $this->categoryManager->selectAll();

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $filmUpdate = $this->cleanData($_POST);

            $errors = $this->testInput($filmUpdate);

            if (empty($errors)) {
                $this->filmManager->update($filmUpdate);
                header('Location: /films/show?id=' . $id);
            }
        }

        return $this->twig->render('Film/edit.html.twig', [
            'film' => $film,
            'categories' => $categories,
            'errors' => $errors
        ]);
    }


    /**
     * Add a new item
     */
    public function add(): string
    {
        $categories = $this->categoryManager->selectAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $film = $this->cleanData($_POST);

            $errors = $this->testInput($film);

            if (empty($errors)) {
                $id = $this->filmManager->insert($film);
                header('Location:/films/show?id=' . $id);
            }
        }

        return $this->twig->render('Film/add.html.twig', ['categories' => $categories, 'errors' => $errors]);
    }


    /**
     * Delete a specific item
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $this->filmManager->delete((int)$id);
            header('Location:/films');
        }
    }

    public function cleanData(array $data): array
    {
        $data = array_map('trim', $data);
        $data = array_map('stripslashes', $data);

        return $data;
    }

    public function testInput(array $data): array
    {
        $errors = [];

        foreach ($data as $key => $input) {
            if (empty($input) && $key != 'id') {
                $errors['empty'] = 'All fields must be filled';
                return $errors;
            }
        }

        if (strlen($data['title']) > 80) {
            $errors['title'] = 'Title must be less than 80 characters';
        }

        if (strlen($data['year']) !== 4) {
            $errors['year'] = 'Year must be in format yyyy';
        }

        return $errors;
    }
}
