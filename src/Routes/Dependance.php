<?php

namespace Juste\Routes;

use Juste\Facades\Helpers\Common;

class Dependance extends Common
{
    protected function resolveDependance(string $param, string | int $pk)
    {
        $possible_model_name = ucfirst(trim($param)) . '.php';

        $possible_model_full_path = MODELS_PATH . DS . $possible_model_name;

        if (file_exists($possible_model_full_path)) {
            $model_namespace = "Juste\\Facades\\Models\\";
            return $model_namespace . $possible_model_name::find($pk);
        }
        return false;
    }
}
