<?php

namespace Juste\Facades\Models;


class Model
{
    protected $table = '';

    protected $fillable = [];

    protected $relations = [];

    protected $primary_key = 'id';

    protected $timestamp = true;

    /**
     * Function de connexion à la base de donnnees
     * @return: pdo instance
     */

    private function db()
    {
        try {
            $bdd = new \PDO("{$_ENV['DB_CONNECTION']}:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            return $bdd;
        } catch (\Throwable $th) {
            die('erreur veillez vous connectez .....');
        }
    }

    /**
     * Renvois toutes les donnees de la base de donnees
     * @param string $
     */
    static function allWithOutRelations()
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table}";
        $stmt = $model->db()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    static function all()
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table}";
        $stmt = $model->db()->query($sql);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $model->relationships($result, true);
    }


    /**
     * prend un id d'une table et renvoie l'occurence correspondante
     * @param int $id
     * @return array
     */
    static function find($id)
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table} WHERE id=?";
        $stmt = $model->db()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $model->relationships($result);
    }

    /**
     * prend une clée et sa valeur d'une table et renvoie l'occurence correspondante
     * @param int|string key
     * @return array
     */
    static function getBy($key, $value)
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table} WHERE {$key}=?";
        $stmt = $model->db()->prepare($sql);
        $stmt->execute([$value]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $model->relationships($result);
    }

    /**
     * prend une clée et sa valeur d'une table et renvoie les valeurs correspondante
     * @param int|string key
     * @return array
     */
    static function filter($clauses)
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table} WHERE {$clauses}";
        $stmt = $model->db()->query($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        //die($sql);
        return $model->relationships($result, true);
    }

    /**
     * prend une clée et sa valeur d'une table et renvoie les valeurs correspondante
     * @param int|string key
     * @return array
     */
    static function where($key, $value, $comparant = '=')
    {
        $model = new static();

        $sql = "SELECT * FROM {$model->table} WHERE {$key} {$comparant} ?";
        $stmt = $model->db()->prepare($sql);
        $stmt->execute([$value]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $model->relationships($result, true);
    }

    /**
     * permet de creer dans la base de données sur la table correspondante
     * @param array $data
     * @return array 
     */
    static function create($data)
    {
        $model = new static();

        $data = array_intersect_key($data, array_flip($model->fillable));

        $data = $model->timestamp ? array_merge($data, [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]) : $data;

        $columns = implode(', ', array_keys($data));
        $placeholders = rtrim(str_repeat('?, ', count($data)), ', ');
        $values = array_values($data);

        $sql = "INSERT INTO {$model->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $model->db()->prepare($sql);

        return $stmt->execute($values) ? $data : false;
    }

    static function update($id, $data)
    {
        $model = new static();

        $data = $model->timestamp ? array_merge($data, [
            'updated_at' => date('Y-m-d H:i:s')
        ]) : $data;

        $setClause = implode('=?, ', array_keys($data)) . '=?';
        $values = array_values($data);
        $values[] = $id;

        $sql = "UPDATE {$model->table} SET {$setClause} WHERE id=?";
        $stmt = $model->db()->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    static function delete($id)
    {
        $model = new static();

        $sql = "DELETE FROM {$model->table} WHERE id=?";
        $stmt = $model->db()->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }

    private function relationships($result, $many = false)
    {
        foreach ($this->relations as $property => $value) {

            if (isset($value['foreign_key'])) {

                $foreign_key = $value['foreign_key'];
                $table = $value['table'];
                $sql = "SELECT * FROM {$table} WHERE {$this->primary_key} = ?";
                $stmt = $this->db()->prepare($sql);

                if (!$many) {
                    $stmt->execute([$result[$foreign_key]]);
                    $related_records = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $result[substr($table, 0, -1)] = $related_records;
                } else {
                    foreach ($result as &$row) {
                        $stmt->execute([$row[$foreign_key]]);
                        $related_records = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $row[substr($table, 0, -1)] = $related_records;
                    }
                }
            }
        }

        return $result;
    }
}
