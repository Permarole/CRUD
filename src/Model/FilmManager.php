<?php

namespace App\Model;

use App\Model\AbstractManager;

class FilmManager extends AbstractManager
{

    public const TABLE = 'films';

    public function insert(array $film): int
    {
        $query = ("INSERT INTO films (title, description, year, category_id)
            VALUES (:title, :description, :year, :category_id)");

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':title', $film['title'], \PDO::PARAM_STR);
        $stmt->bindValue(':description', $film['description'], \PDO::PARAM_STR);
        $stmt->bindValue(':year', $film['year'], \PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $film['category'], \PDO::PARAM_INT);

        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $film): void
    {
        $query = ("UPDATE films 
        SET title=:title, description=:description, year=:year, category_id=:category_id 
        WHERE id=:id;");

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':title', $film['title'], \PDO::PARAM_STR);
        $stmt->bindValue(':description', $film['description'], \PDO::PARAM_STR);
        $stmt->bindValue(':year', $film['year'], \PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $film['category'], \PDO::PARAM_INT);
        $stmt->bindValue(':id', $film['id'], \PDO::PARAM_INT);


        $stmt->execute();
    }
}
