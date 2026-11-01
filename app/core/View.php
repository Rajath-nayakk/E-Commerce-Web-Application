<?php
declare(strict_types=1);

namespace App\Core;

require_once __DIR__ . '/helpers.php';

/**
 * Class View
 * 
 * Secure template renderer. Escapes HTML by default, supports master layouts,
 * partials, component alerts, and helper functions for links and assets.
 */
class View
{
    /**
     * Render a view file with optional data and layout
     */
    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Extract data variables into local scope
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<h1>View Not Found</h1><p>Missing template: " . htmlspecialchars($viewFile) . "</p>";
            return;
        }

        // Buffer the view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // If layout requested, wrap inside layout
        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $layout) . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }
}
