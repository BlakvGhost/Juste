<?php

namespace Juste\Facades\Helpers;

class Common
{
    /**
     * Returns an array with all the contents of the global $_POST variable escaped with htmlentities().
     */
    protected function posts(): array
    {
        $posts = [];
        foreach ($_POST as $key => $post) {
            if (!empty($post)) {
                $posts[$key] = htmlentities($post);
            }
        }
        return $posts;
    }

    /**
     * Returns the value of the given key from the global $_SERVER array.
     */
    protected function server(string $key): string
    {
        $data = $_SERVER[$key];

        return $data;
    }

    /**
     *  Returns the value of the given key from either the global $_POST or $_GET variables, with the option to specify a default value if the key is not set.
     */
    protected function input(string $key, string $default = ''): string
    {
        $data = $_REQUEST[$key];

        return !empty($data) ? htmlentities($data) : $default;
    }

    /**
     *  Returns the file uploaded with the given key from the global $_FILES variable, or redirects back to the previous page with an error message if no file was uploaded.
     */
    protected function file(string $key)
    {
        $file = $_FILES[$key];

        if ((isset($file) && $file['error'] == 0)) return $file;

        return $this->back()->with("Le fichier ne peut pas etre null");
    }

    /**
     * Redirects the user to the given path using the header() function
     */
    protected function redirecTo(string $path = '')
    {
        header('Location: ' . $path);
        return $this;
    }

    /**
     * Returns the sanitized value of the given key from the global $_POST variable, with the option to validate that the key exists and is not empty.
     */
    protected function sanitize_post(string $key, bool $strict = true)
    {

        if ($strict) {

            if (!isset($_POST[$key]) || empty($_POST[$key])) {

                $this->back()->with("Le champs " . $key . " ne peut pas etre vide");

                exit();
            }
            return htmlentities($_POST[$key]);
        }

        return htmlentities($_POST[$key]);
    }

    /**
     * Redirects the user back to the previous page
     */
    protected function back()
    {
        $to = $_SERVER['HTTP_REFERER'] ?? '/';
        //die("Hey !");
        header("Location: " . $to);
        return $this;
    }

    /**
     * Sets a message on the session with the given key (defaulting to 'error').
     */
    protected function with(string $message, $key = 'error')
    {
        $this->setDataOnSession($key, $message);

        return $this;
    }

    /**
     * Returns a JSON-encoded string of the given array.
     */
    protected function json(array $data)
    {
        return json_encode($data);
    }

    /**
     * Returns current authenticated user information or an empty array if not authenticated
     */
    protected function user($attr = false)
    {

        return isset($_SESSION['user']) ?
            ($attr ? $_SESSION['user'][$attr] : $_SESSION['user']) :
            [];
    }

    protected function store_media($file, string $newFileName): string
    {
        $base_path = UPLOAD_BASE_NAME . DS . $newFileName . '-' . date("Y-m-d-H-s-i") . '.' . pathinfo($file['name'])['extension'];
        $path = ASSETS_PATH . DS . $base_path;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            return $base_path;
        } else {
            $this->back()->with("Erreur d'importation du fichier");
        }
    }

    protected function setDataOnSession($key, $message)
    {
        return $_SESSION[$key] = $message;
    }

    protected function getDataOnSession($key)
    {
        return $_SESSION[$key] ?? [];
    }

    protected function setErrorMessageOnSession($message)
    {
        return self::setDataOnSession('error', $message);
    }

    /**
     * make var_dump() and exit() in pre tag
     */
    protected function dd($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        die();
    }

    /**
     * Retourne la route d'un url selon son alias
     * @param string $alias The alias of the Route
     */
    protected function route(string $alias): string
    {
        $routes = isset($_SESSION['routes']) ? $_SESSION['routes'] : [];

        return isset($routes[$alias]) ? '/' . $routes[$alias] : '/';
    }
}
