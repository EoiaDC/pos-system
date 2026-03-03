<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): string
    {
        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            return "<h1>View not found: " . htmlspecialchars($view) . "</h1>";
        }
        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../../views/layout/app.php';
        if (file_exists($layoutFile)) {
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }
}