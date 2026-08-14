<?php
session_start();
require '/app/src/config/api.config.php';
require '/app/src/utils/api.php';

$login = api_login('budi.santoso@example.com', 'password');
echo 'login ok=' . var_export($login['ok'], true) . ' status=' . $login['status'] . PHP_EOL;
if (!empty($login['data']['data']['user_id'])) {
    echo 'logged user_id=' . $login['data']['data']['user_id'] . PHP_EOL;
}

$r1 = api_get(API_RESERVATIONS . '?limit=100');
echo 'r1 (no param): ok=' . var_export($r1['ok'], true) . ' status=' . $r1['status'];
if (isset($r1['data']['data']['total'])) {
    echo ' total=' . $r1['data']['data']['total'];
    $items = $r1['data']['data']['data'] ?? [];
    $codes = array_column($items, 'booking_code');
    echo ' codes=[' . implode(',', array_slice($codes, 0, 6)) . ']';
} else {
    echo ' msg=' . ($r1['data']['message'] ?? json_encode($r1['data']));
}
echo PHP_EOL;

$r2 = api_get(API_RESERVATIONS . '?user_id=6&limit=100');
echo 'r2 (user_id=6): ok=' . var_export($r2['ok'], true) . ' status=' . $r2['status'];
if (isset($r2['data']['data']['total'])) {
    echo ' total=' . $r2['data']['data']['total'];
    $items = $r2['data']['data']['data'] ?? [];
    $codes = array_column($items, 'booking_code');
    echo ' codes=[' . implode(',', array_slice($codes, 0, 6)) . ']';
} else {
    echo ' msg=' . ($r2['data']['message'] ?? json_encode($r2['data']));
}
echo PHP_EOL;