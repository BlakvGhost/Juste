<?php

namespace Juste\Facades\Controllers;

use Juste\Facades\Helpers\Common;

class Controller extends Common
{

    /**
     * This method returns a view by concatenating the view path with the provided $view string. It checks whether the file exists and, if it does, returns an array with the view path, a context array (if provided) and a title (if provided). If the file does not exist, it returns a 404 view path with global context data.
     * @param string $viewPath
     * @return array full view path or 404 view path with context globale data
     */
    protected function render($view, $title = '', $context = null)
    {
        $view  = VIEW_PATH . DS . $view . '.php';

        $view = file_exists($view) ? $view : ERRORS_VIEW_PATH . DS . '404.php';

        return [
            'view_path' => $view,
            'context' => $context ?? [],
            'title' => $title
        ];
    }

    /**
     * This method checks whether the user is authenticated by calling the mustAuthenticate() method. If the user is authenticated, it checks whether the user's role is included in the $user_type array (or defaults to ['Admin']). If the user's role matches one of the roles in $user_type, the method returns nothing. If the user's role does not match any of the roles in $user_type, the method redirects back and returns an error message.
     */
    protected function can(array $user_type = null, string $column = 'roles')
    {
        $this->mustAuthenticate();

        $user_type = $user_type ?? ['Admin'];

        $user_role = $this->user($column);

        foreach ($user_type as $role) {
            if ($user_role === $role) return;
        }

        return $this->back()->with("Access Denied to this Ressource");
    }

    /**
     * This method checks whether the user is authenticated by checking if the user array is not empty. If $statut is true, it returns nothing if the user is authenticated and an error message if the user is not authenticated. If $statut is false, it returns nothing if the user is not authenticated and an error message if the user is authenticated. It uses the back() method to redirect the user back to the previous page.
     */
    protected function mustAuthenticate(bool $statut = true)
    {
        if ($statut) {
            count($this->user()) ? null : $this->back()->with("Connect You first before accessing this resource");
        } else {
            !count($this->user()) ? null : $this->back()->with("You Can't access this resource during login time");
        }
    }
}
