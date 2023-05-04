<?php

namespace Juste\Facades\Models;

class Common extends Model
{
    private $instance;

    private $data;

    private $sql;

    public function __construct(Model $instance, $data, string $sql)
    {
        $this->instance = $instance;
        $this->data = $data;
        $this->sql = $sql;
    }

    /**
     * Function de connexion à la base de donnnees
     * @return: pdo instance
     */

    protected function db()
    {
        try {
            $bdd = new \PDO('mysql:host=localhost;dbname=saturnin_mentor_project;charset=utf8', 'root', '');
            return $bdd;
        } catch (\Throwable $th) {
            die('erreur veillez vous connectez .....');
        }
    }

    protected function collection()
    {
    }

    public function get()
    {
        return $this->data;
    }
}
