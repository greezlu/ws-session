<?php
/** Copyright github.com/greezlu */

declare(strict_types = 1);

namespace WebServer\Core;

/**
 * @package greezlu/ws-session
 */
class Session
{
    protected const FORBIDDEN_DATA_KEY = [
        'user_id',
        'login_success',
        'success',
        'errors'
    ];

    /**
     * Start session.
     *
     * @return void
     */
    static public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Session destroy.
     *
     * @return void
     */
    static public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = [];
        }
    }

    /**
     * Get current user ID.
     *
     * @return int|null
     */
    static public function getUserId(): ?int
    {
        return self::isLoggedIn() && isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
            ? (int)$_SESSION['user_id']
            : null;
    }

    /**
     * Get bool value if user is logged in.
     *
     * @return bool
     */
    static public function isLoggedIn(): bool
    {
        return !empty($_SESSION['login_success'])
            && $_SESSION['login_success'] === true
            && !empty($_SESSION['user_id']);
    }

    /**
     * Login user. Set user id.
     *
     * @param int $userId
     * @return void
     */
    static public function login(int $userId): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['login_success'] = true;
    }

    /**
     * Logout user.
     *
     * @return void
     */
    static public function logout(): void
    {
        unset($_SESSION['user_id']);
        $_SESSION['login_success'] = false;
    }

    /**
     * Get and clear error messages list from current session.
     *
     * @return array
     */
    static public function getErrors(): array
    {
        $errors = [];

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        return $errors;
    }

    /**
     * Get and clear success messages list from current session.
     *
     * @return array
     */
    static public function getSuccess(): array
    {
        $success = [];

        if (isset($_SESSION['success'])) {
            $success = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        return $success;
    }

    /**
     * Add error messages to current session.
     *
     * @param string|string[] $errors
     * @return void
     */
    static public function addErrors($errors): void
    {
        if (is_string($errors)) {
            $_SESSION['errors'][] = $errors;
        } else if (is_array($errors)) {
            foreach ($errors as $message) {
                $_SESSION['errors'][] = $message;
            }
        }
    }

    /**
     * Add success message to current session.
     *
     * @param string|string[] $success
     * @return void
     */
    static public function addSuccess($success): void
    {
        if (is_string($success)) {
            $_SESSION['success'][] = $success;
        } else if (is_array($success)) {
            foreach ($success as $message) {
                $_SESSION['success'][] = $message;
            }
        }
    }

    /**
     * Get data from session or null.
     *
     * @param string|null $key
     * @return mixed
     */
    static public function getData(string $key = null)
    {
        if (!is_null($key) && in_array($key, static::FORBIDDEN_DATA_KEY)) {
            return null;
        }

        $data = array_diff($_SESSION, static::FORBIDDEN_DATA_KEY);

        return !is_null($key)
            ? $data[$key] ?? null
            : $data;
    }

    /**
     * Set data to session.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    static public function setData(string $key, $value): void
    {
        if (in_array($key, static::FORBIDDEN_DATA_KEY)) {
            return;
        }

        $_SESSION[$key] = $value;
    }
}
