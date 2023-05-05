<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Helpers\Common;

class Dependance extends Common
{
    protected function resolveDependance(string $param, string | int $pk)
    {
        $possible_model_name = ucfirst(trim($param));

        $possible_model_full_path = MODELS_PATH . DS . $possible_model_name  . '.php';

        if (file_exists($possible_model_full_path)) {
            $model_namespace = "App\\Models\\";
            $model = $model_namespace . $possible_model_name;

            return $model::find($pk);
        }
        return false;
    }
}
