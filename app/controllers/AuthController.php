<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\PasswordReset;

/**
 * Class AuthController
 * 
 * Handles user registration, authentication, logout, rate limiting,
 * and token-based password reset flows.
 */
class AuthController extends Controller
{
    /**
     * Show Sign In Page
     */
    public function showLogin(Request $request): void
    {
        $config = require dirname(__DIR__) . '/config/config.php';
        $this->view('auth/login', [
            'title'  => 'Sign In',
            'isDemo' => !empty($config['app_demo'])
        ]);
    }

    /**
     * Process Sign In submission
     */
    public function login(Request $request): void
    {
        $this->validateCsrf($request);

        $email = strtolower(trim((string) $request->post('email', '')));
        $password = (string) $request->post('password', '');

        // 1. Check Rate-Limiting by email / IP
        if (Auth::isRateLimited($email)) {
            $remaining = ceil(Auth::getLockoutRemaining($email) / 60);
            Session::flash('danger', "Too many failed login attempts. Account temporarily locked. Please try again in {$remaining} minutes.");
            $this->redirect('/login');
        }

        // 2. Validate input fields
        $this->validate([
            'email'    => $email,
            'password' => $password
        ], [
            'email'    => 'required|email',
            'password' => 'required'
        ], $request, '/login');

        // 3. Find user by email
        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            $attempts = Auth::incrementLoginAttempts($email);
            $config = require dirname(__DIR__) . '/config/config.php';
            $maxAttempts = $config['login_max_att'] ?? 5;
            $left = max(0, $maxAttempts - $attempts);

            if ($left > 0) {
                Session::flash('danger', "Invalid email or password. {$left} attempts remaining before temporary lockout.");
            } else {
                Session::flash('danger', "Too many failed attempts. Login locked for 15 minutes.");
            }
            $this->redirect('/login');
        }

        // 4. Authenticate, regenerate session ID & merge guest cart
        Auth::login($user);

        Session::flash('success', "Welcome back, " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "!");

        // Redirect admin to dashboard, normal customer to home or intended url
        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        } else {
            $intended = Session::get('_intended_url', '/');
            Session::remove('_intended_url');
            $this->redirect($intended);
        }
    }

    /**
     * Process Sign Out
     */
    public function logout(Request $request): void
    {
        $this->validateCsrf($request);
        Auth::logout();
        Session::flash('info', 'You have been successfully signed out.');
        $this->redirect('/login');
    }

    /**
     * Show Registration Page
     */
    public function showRegister(Request $request): void
    {
        $this->view('auth/register', [
            'title' => 'Create an Account',
        ]);
    }

    /**
     * Process Registration submission
     */
    public function register(Request $request): void
    {
        $this->validateCsrf($request);

        $name = trim((string) $request->post('name', ''));
        $email = strtolower(trim((string) $request->post('email', '')));
        $password = (string) $request->post('password', '');
        $passwordConfirm = (string) $request->post('password_confirm', '');

        // Validate user inputs
        $this->validate([
            'name'             => $name,
            'email'            => $email,
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        ], [
            'name'             => 'required|min:2|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6',
            'password_confirm' => 'required|matches:password'
        ], $request, '/register');

        // Create new customer account
        $userId = User::register($name, $email, $password, 'user');
        $user = User::find($userId);

        // Auto login
        Auth::login($user);

        Session::flash('success', "Welcome to NovaTech Gear, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "! Your account is active.");
        $this->redirect('/');
    }

    /**
     * Show Forgot Password Page
     */
    public function showForgotPassword(Request $request): void
    {
        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password',
        ]);
    }

    /**
     * Handle Forgot Password submission
     */
    public function sendResetLink(Request $request): void
    {
        $this->validateCsrf($request);

        $email = strtolower(trim((string) $request->post('email', '')));

        $this->validate([
            'email' => $email
        ], [
            'email' => 'required|email'
        ], $request, '/forgot-password');

        $user = User::findByEmail($email);

        if ($user) {
            $rawToken = PasswordReset::createToken((int) $user['id']);
            $resetUrl = "/reset-password?token=" . urlencode($rawToken);

            // In production this is emailed. In our local college environment,
            // we provide an on-screen demo banner with the clickable reset link!
            Session::flash('success', "Password reset link generated successfully! <br><a href='{$resetUrl}' class='fw-bold text-decoration-underline text-white'>Click here to reset your password</a> (Simulated email delivery).");
        } else {
            // Security best practice: don't reveal if email exists or not
            Session::flash('info', "If an account exists with that email, a password reset link has been dispatched.");
        }

        $this->redirect('/forgot-password');
    }

    /**
     * Show Reset Password Page
     */
    public function showResetPassword(Request $request): void
    {
        $token = (string) $request->get('token', '');
        $record = PasswordReset::findValidToken($token);

        if (!$record) {
            Session::flash('danger', 'Password reset token is invalid or has expired. Please request a new one.');
            $this->redirect('/forgot-password');
        }

        $this->view('auth/reset_password', [
            'title' => 'Reset Your Password',
            'token' => $token,
            'email' => $record['email']
        ]);
    }

    /**
     * Process Reset Password submission
     */
    public function resetPassword(Request $request): void
    {
        $this->validateCsrf($request);

        $token = (string) $request->post('token', '');
        $password = (string) $request->post('password', '');
        $passwordConfirm = (string) $request->post('password_confirm', '');

        $this->validate([
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        ], [
            'password'         => 'required|min:6',
            'password_confirm' => 'required|matches:password'
        ], $request, "/reset-password?token=" . urlencode($token));

        $record = PasswordReset::findValidToken($token);
        if (!$record) {
            Session::flash('danger', 'Reset token has expired or is invalid.');
            $this->redirect('/forgot-password');
        }

        // Update password
        User::updatePassword((int) $record['user_id'], $password);

        // Invalidate token
        PasswordReset::deleteToken($token);

        // Reset rate limits for this user
        Auth::clearLoginAttempts($record['email']);

        Session::flash('success', 'Your password has been successfully reset. Please sign in with your new password.');
        $this->redirect('/login');
    }
}

