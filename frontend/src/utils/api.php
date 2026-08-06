<?php
/**
 * ============================================
 * API CLIENT - AKSES BACKEND DARI FRONTEND
 * ============================================
 * Client cURL server-side yang mem-forward request ke backend Laravel.
 * Cookie sesi backend (Laravel) disimpan per-sesi frontend di file cookie,
 * sehingga login frontend == login backend tanpa masalah CORS.
 *
 * Penggunaan:
 *   require_once __DIR__ . '/../config/api.config.php';
 *   require_once __DIR__ . '/api.php';
 *
 *   $result = api_login('user@mail.com', 'password');
 *   $reservations = api_get('/reservations');
 */

if (!function_exists('api_cookie_file')) {
    /**
     * File cookie untuk menyimpan sesi backend (per-sesi frontend).
     */
    function api_cookie_file()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kafiber_api_' . md5(session_id()) . '.cookie';
    }
}

if (!function_exists('api_request')) {
    /**
     * Kirim request HTTP ke backend API.
     *
     * @param string $method  GET / POST / PUT / PATCH / DELETE
     * @param string $uri     Path endpoint, contoh '/auth/login'
     * @param array|null $data  Data JSON (untuk selain GET)
     * @param int $timeout    Timeout detik
     *
     * @return array{ok: bool, status: int, data: array}
     */
    function api_request($method, $uri, $data = null, $timeout = 15)
    {
        $method = strtoupper($method);
        $url = API_BASE_URL . '/' . ltrim($uri, '/');

        $ch = curl_init($url);

        $headers = ['Accept: application/json'];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_COOKIEFILE => api_cookie_file(),
            CURLOPT_COOKIEJAR => api_cookie_file(),
        ];

        if ($method === 'GET') {
            $options[CURLOPT_HTTPGET] = true;
        } else {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
            if ($data !== null) {
                $headers[] = 'Content-Type: application/json';
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => ['message' => 'Backend tidak dapat dihubungi. ' . $error],
            ];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            $decoded = ['message' => 'Respons backend tidak valid.', 'raw' => $body];
        }

        return [
            'ok' => $status >= 200 && $status < 300,
            'status' => $status,
            'data' => $decoded,
        ];
    }
}

if (!function_exists('api_login')) {
    function api_login($email, $password)
    {
        return api_request('POST', API_AUTH_LOGIN, [
            'email' => $email,
            'password' => $password,
        ]);
    }
}

if (!function_exists('api_register')) {
    function api_register(array $payload)
    {
        return api_request('POST', API_AUTH_REGISTER, $payload);
    }
}

if (!function_exists('api_logout')) {
    function api_logout()
    {
        return api_request('POST', API_AUTH_LOGOUT);
    }
}

if (!function_exists('api_me')) {
    function api_me()
    {
        return api_request('GET', API_AUTH_ME);
    }
}

if (!function_exists('api_get')) {
    function api_get($uri)
    {
        return api_request('GET', $uri);
    }
}

if (!function_exists('api_post')) {
    function api_post($uri, array $data)
    {
        return api_request('POST', $uri, $data);
    }
}

if (!function_exists('set_frontend_session_from_user')) {
    /**
     * Sinkronkan data user dari backend ke sesi frontend.
     */
    function set_frontend_session_from_user(array $user)
    {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'] ?? null;
        $_SESSION['user_name'] = $user['name'] ?? 'Akun';
        $_SESSION['user_email'] = $user['email'] ?? '';
        $_SESSION['role'] = $user['role'] ?? 'customer';
    }
}

if (!function_exists('frontend_logout')) {
    /**
     * Logout dari backend, hapus cookie sesi backend, dan bersihkan sesi frontend.
     */
    function frontend_logout()
    {
        $cookieFile = api_cookie_file();

        try {
            api_logout();
        } catch (Throwable $e) {
            // Abaikan error backend saat logout.
        }

        if (is_file($cookieFile)) {
            @unlink($cookieFile);
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}

if (!function_exists('api_error_message')) {
    /**
     * Ambil pesan error terbaik dari respons API (Laravel validation dsb).
     */
    function api_error_message(array $result, $fallback = 'Terjadi kesalahan.')
    {
        $data = $result['data'] ?? [];

        if (!empty($data['message'])) {
            return (string) $data['message'];
        }

        if (!empty($data['errors']) && is_array($data['errors'])) {
            foreach ($data['errors'] as $fieldErrors) {
                if (is_array($fieldErrors) && !empty($fieldErrors)) {
                    return (string) $fieldErrors[0];
                }
            }
        }

        return $fallback;
    }
}
