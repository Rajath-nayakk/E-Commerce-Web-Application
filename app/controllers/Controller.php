<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Response;
use App\Core\Session;
use App\Core\Csrf;
use App\Core\Validator;
use App\Core\Request;

/**
 * Class Controller
 * 
 * Abstract base controller providing standard view rendering, JSON responses,
 * redirect helpers, CSRF verification, and input validation.
 */
abstract class Controller
{
    /**
     * Render an HTML template with optional layout
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Send a JSON response
     */
    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    /**
     * Redirect with optional flash message
     */
    protected function redirect(string $url, ?string $flashType = null, ?string $flashMessage = null): void
    {
        if ($flashType !== null && $flashMessage !== null) {
            Session::flash($flashType, $flashMessage);
        }
        Response::redirect($url);
    }

    /**
     * Validate CSRF token for state-changing requests
     */
    protected function validateCsrf(Request $request): void
    {
        if (!Csrf::validateRequest()) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
                Response::error('Invalid or expired CSRF token.', 403);
            }
            Session::flash('danger', 'Security validation failed (CSRF token expired). Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    /**
     * Run input validation and flash old inputs on failure
     */
    protected function validate(array $data, array $rules, Request $request, string $redirectBackUrl): Validator
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            // Save inputs to session so old() can re-populate forms
            Session::set('_old_input', $data);
            Session::set('_errors', $validator->errors());
            Session::flash('danger', $validator->firstError() ?? 'Please correct the errors in the form.');

            if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
                Response::error('Validation failed.', 422, $validator->errors());
            }

            $this->redirect($redirectBackUrl);
        }

        // Clear previous input memory on success
        Session::remove('_old_input');
        Session::remove('_errors');

        return $validator;
    }
}
