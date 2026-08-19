<?php
/**
 * export_laporan.php — Unduh Laporan & Analitik (Admin)
 * Mendukung: type=pdf (Dompdf) dan type=excel (PhpSpreadsheet).
 *
 * Penggunaan:
 *   export_laporan.php?type=pdf&from=2026-01-01&to=2026-01-31
 *   export_laporan.php?type=excel&from=2026-01-01&to=2026-01-31
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Dompdf\Dompdf;
use Dompdf\Options;

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn || $role !== 'admin') {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/src/config/api.config.php';
require_once __DIR__ . '/src/utils/api.php';
require_once __DIR__ . '/vendor/autoload.php';

set_time_limit(0);
ini_set('memory_limit', '256M');

$type = $_GET['type'] ?? 'pdf';
if (!in_array($type, ['pdf', 'excel'], true)) {
    $type = 'pdf';
}

$filterFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['from'] ?? '')) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
$filterTo   = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['to'] ?? ''))   ? $_GET['to']   : date('Y-m-d');
$restoFilter = (int) ($_GET['restaurant_id'] ?? 0);

// Ambil daftar restoran (untuk nama laporan & validasi filter)
$restaurants = [];
$restoResult = api_get(API_RESTAURANTS . '?limit=200');
if ($restoResult['ok']) {
    $raw = $restoResult['data']['data'] ?? [];
    $restaurants = $raw['data'] ?? $raw;
}
$selectedRestoName = 'Semua Restoran';
foreach ($restaurants as $resto) {
    if ((int) ($resto['restaurant_id'] ?? 0) === $restoFilter) {
        $selectedRestoName = $resto['name'] ?? 'Restoran';
        break;
    }
}

// Ambil data dari backend
$reservations = [];
$resResult = api_get(API_RESERVATIONS . '?limit=500');
if ($resResult['ok']) {
    $raw = $resResult['data']['data'] ?? [];
    $reservations = $raw['data'] ?? $raw;
}

$payments = [];
$payResult = api_get(API_PAYMENTS . '?limit=500');
if ($payResult['ok']) {
    $raw = $payResult['data']['data'] ?? [];
    $payments = $raw['data'] ?? $raw;
}

$totalTables = 20;
$tablesResult = api_get(API_TABLES . '?limit=500');
if ($tablesResult['ok']) {
    $raw = $tablesResult['data']['data'] ?? [];
    $tableList = $raw['data'] ?? $raw;
    if (!empty($tableList)) {
        $totalTables = count($tableList);
    }
}

$filtered = array_values(array_filter($reservations, function ($r) use ($filterFrom, $filterTo, $restoFilter) {
    $date = substr((string) ($r['reservation_date'] ?? ''), 0, 10);
    if ($date < $filterFrom || $date > $filterTo) {
        return false;
    }
    if ($restoFilter > 0 && (int) ($r['table']['restaurant']['restaurant_id'] ?? 0) !== $restoFilter) {
        return false;
    }
    return true;
}));

$filteredPayments = array_filter($payments, function ($p) use ($filterFrom, $filterTo) {
    $date = substr((string) ($p['paid_at'] ?? $p['created_at'] ?? ''), 0, 10);
    return $date === '' || ($date >= $filterFrom && $date <= $filterTo);
});

$totalReservations = count($filtered);
$totalPaid = count(array_filter($filtered, fn ($r) => ($r['payment_status'] ?? '') === 'paid'));
$noShow = count(array_filter($filtered, fn ($r) => ($r['status'] ?? '') === 'no_show'));
$completed = count(array_filter($filtered, fn ($r) => ($r['status'] ?? '') === 'completed'));

$revenue = 0.0;
foreach ($filtered as $r) {
    if (($r['payment_status'] ?? '') === 'paid') {
        $revenue += (float) ($r['deposit_amount'] ?? 0);
    }
}
foreach ($filteredPayments as $p) {
    if (($p['status'] ?? '') === 'success' || ($p['status'] ?? '') === 'paid') {
        $revenue += (float) ($p['amount'] ?? 0);
    }
}

$hourCounts = [];
foreach ($filtered as $r) {
    $t = (string) ($r['reservation_time'] ?? '');
    $hour = substr($t, 0, 5);
    if ($hour !== '') {
        $hourCounts[$hour] = ($hourCounts[$hour] ?? 0) + 1;
    }
}
arsort($hourCounts);
$peakHour = $hourCounts ? key($hourCounts) . ' - ' . (date('H:i', strtotime(key($hourCounts) . ':00') + 7200)) : '--';

$daysSpan = max(1, (int) ((strtotime($filterTo) - strtotime($filterFrom)) / 86400) + 1);
$occupancy = $totalReservations > 0 && $totalTables > 0
    ? round((($totalReservations / $daysSpan) / $totalTables) * 100, 1)
    : 0;
$noShowRate = $totalReservations > 0 ? round(($noShow / $totalReservations) * 100, 1) : 0;

function fmt_rupiah($num) {
    return 'Rp ' . number_format((float) $num, 0, ',', '.');
}

function fmt_date($date) {
    $ts = strtotime($date);
    if (!$ts) {
        return $date;
    }
    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

function status_humanize($status) {
    $map = [
        'pending' => 'Pending',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'no_show' => 'No-show',
        'paid' => 'Lunas',
        'success' => 'Sukses',
        'unpaid' => 'Belum Bayar',
        'refunded' => 'Refund',
    ];
    $status = (string) ($status ?? '');
    return isset($map[$status]) ? $map[$status] : ucfirst(str_replace('_', ' ', $status));
}

$restoName = $selectedRestoName !== 'Semua Restoran' ? $selectedRestoName : 'Kafiber Restoran';

$periodText = fmt_date($filterFrom) . ' s.d. ' . fmt_date($filterTo);
$generatedAt = date('d M Y H:i');

if ($type === 'excel') {
    $spreadsheet = new Spreadsheet();

    // ---- Sheet Ringkasan ----
    $ringkasan = $spreadsheet->getActiveSheet();
    $ringkasan->setTitle('Ringkasan');
    $ringkasan->setCellValue('A1', 'LAPORAN & ANALITIK RESTORAN');
    $ringkasan->setCellValue('A2', $restoName);
    $ringkasan->setCellValue('A3', 'Periode: ' . $periodText);
    $ringkasan->setCellValue('A4', 'Dibuat: ' . $generatedAt);

    $ringkasan->mergeCells('A1:C1');
    $ringkasan->mergeCells('A2:C2');
    $ringkasan->mergeCells('A3:C3');
    $ringkasan->mergeCells('A4:C4');
    $ringkasan->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $ringkasan->getStyle('A2')->getFont()->setBold(true)->setSize(12);
    $ringkasan->getStyle('A1:A4')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('5E392E'));

    $ringkasan->setCellValue('A6', 'METRIK');
    $ringkasan->getStyle('A6')->getFont()->setBold(true)->setSize(13);

    $summaryRows = [
        ['Total Reservasi', $totalReservations],
        ['Pembayaran Lunas', $totalPaid],
        ['Kedatangan (Selesai)', $completed],
        ['No-show', $noShow],
        ['Pendapatan', fmt_rupiah($revenue)],
        ['Occupancy Rate', $occupancy . '%'],
        ['Tingkat No-show', $noShowRate . '%'],
        ['Peak Hours', $peakHour],
        ['Jumlah Meja', $totalTables],
    ];

    $r = 7;
    foreach ($summaryRows as $i => $row) {
        $ringkasan->setCellValue('A' . $r, $row[0]);
        $ringkasan->setCellValue('B' . $r, $row[1]);
        $ringkasan->getStyle('A' . $r)->getFont()->setBold(true);
        $ringkasan->getStyle('A' . $r . ':B' . $r)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB($i % 2 === 0 ? 'FCFAF7' : 'F3ECE2');
        $r++;
    }

    $ringkasan->getColumnDimension('A')->setWidth(28);
    $ringkasan->getColumnDimension('B')->setWidth(22);

    // ---- Sheet Detail Reservasi ----
    $detail = $spreadsheet->createSheet();
    $detail->setTitle('Detail Reservasi');
    $detail->setCellValue('A1', 'DETAIL RESERVASI — ' . $periodText);

    $headers = ['No', 'Kode', 'Nama', 'Restoran', 'Meja', 'Tanggal', 'Waktu', 'Tamu', 'Status', 'Pembayaran', 'Deposit'];
    $col = 1;
    foreach ($headers as $h) {
        $detail->setCellValue(Coordinate::stringFromColumnIndex($col) . 2, $h);
        $col++;
    }

    $rowIdx = 3;
    $i = 1;
    foreach ($filtered as $res) {
        $detail->setCellValue('A' . $rowIdx, $i);
        $detail->setCellValue('B' . $rowIdx, $res['booking_code'] ?? '-');
        $detail->setCellValue('C' . $rowIdx, $res['user']['name'] ?? 'Tamu');
        $detail->setCellValue('D' . $rowIdx, $res['table']['restaurant']['name'] ?? '-');
        $detail->setCellValue('E' . $rowIdx, $res['table']['table_number'] ?? '-');
        $detail->setCellValue('F' . $rowIdx, substr((string) ($res['reservation_date'] ?? ''), 0, 10));
        $detail->setCellValue('G' . $rowIdx, substr((string) ($res['reservation_time'] ?? ''), 0, 5));
        $detail->setCellValue('H' . $rowIdx, (int) ($res['number_of_guest'] ?? 0));
        $detail->setCellValue('I' . $rowIdx, status_humanize($res['status'] ?? ''));
        $detail->setCellValue('J' . $rowIdx, status_humanize($res['payment_status'] ?? ''));
        $detail->setCellValue('K' . $rowIdx, (float) ($res['deposit_amount'] ?? 0));
        $detail->getStyle('K' . $rowIdx)->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');
        $rowIdx++;
        $i++;
    }

    $lastRow = $rowIdx - 1;
    if ($lastRow >= 3) {
        $headerRange = 'A2:K2';
        $detail->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $detail->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('5E392E');
        $detail->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $detail->getStyle('A2:K' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        foreach ($headers as $idx => $h) {
            $detail->getColumnDimension(Coordinate::stringFromColumnIndex($idx + 1))->setAutoSize(true);
        }
    }

    // Footer sheet detail
    $detail->setCellValue('A' . ($lastRow + 2), 'Total baris: ' . count($filtered));
    $detail->setCellValue('A' . ($lastRow + 3), 'Dibuat: ' . $generatedAt);

    $writer = new Xlsx($spreadsheet);
    $filename = 'laporan_reservasi_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}

// ---- PDF (Dompdf) ----
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { font-family: "DejaVu Sans", sans-serif; }
    body { color: #201913; font-size: 10px; line-height: 1.45; }
    .header { border-bottom: 3px solid #5E392E; padding-bottom: 10px; margin-bottom: 14px; }
    .header h1 { margin: 0; font-size: 20px; color: #5E392E; }
    .header .sub { color: #66574B; font-size: 10px; margin-top: 3px; }
    .header .meta { margin-top: 6px; font-size: 9px; color: #8A5D49; }
    h2 { font-size: 13px; color: #5E392E; margin: 16px 0 6px; }
    table { width: 100%; border-collapse: collapse; }
    .summary td, .summary th { padding: 5px 8px; border: 1px solid #EADFD4; }
    .summary .label { background: #FCFAF7; font-weight: bold; color: #8A5D49; width: 32%; }
    .summary .value { text-align: right; }
    .detail th { background: #5E392E; color: #fff; padding: 5px 6px; text-align: left; font-size: 8.5px; }
    .detail td { padding: 4px 6px; border: 1px solid #EADFD4; font-size: 8.5px; }
    .detail tr:nth-child(even) td { background: #FCFAF7; }
    .footer { margin-top: 16px; font-size: 8px; color: #A39A8F; text-align: center; }
    .right { text-align: right; }
</style>
</head>
<body>
<div class="header">
    <h1>' . e($restoName) . '</h1>
    <div class="sub">Laporan &amp; Analitik Reservasi</div>
    <div class="meta">Periode: ' . e($periodText) . ' &nbsp;|&nbsp; Dibuat: ' . e($generatedAt) . ' &nbsp;|&nbsp; Diekspor oleh Admin</div>
</div>

<h2>Ringkasan Metrik</h2>
<table class="summary">
    <tr><td class="label">Total Reservasi</td><td class="value">' . $totalReservations . '</td>
        <td class="label">Pendapatan</td><td class="value">' . fmt_rupiah($revenue) . '</td></tr>
    <tr><td class="label">Pembayaran Lunas</td><td class="value">' . $totalPaid . '</td>
        <td class="label">Occupancy Rate</td><td class="value">' . $occupancy . '%</td></tr>
    <tr><td class="label">Kedatangan (Selesai)</td><td class="value">' . $completed . '</td>
        <td class="label">Tingkat No-show</td><td class="value">' . $noShowRate . '%</td></tr>
    <tr><td class="label">No-show</td><td class="value">' . $noShow . '</td>
        <td class="label">Peak Hours</td><td class="value">' . e($peakHour) . '</td></tr>
</table>

<h2>Detail Reservasi (' . count($filtered) . ' data)</h2>';

if (count($filtered) === 0) {
    $html .= '<p style="color:#A39A8F;">Tidak ada reservasi pada rentang tanggal ini.</p>';
} else {
    $html .= '<table class="detail">
        <thead>
            <tr>
                <th>No</th><th>Kode</th><th>Nama</th><th>Restoran</th><th>Meja</th>
                <th>Tanggal</th><th>Waktu</th><th>Tamu</th><th>Status</th><th>Pembayaran</th><th class="right">Deposit</th>
            </tr>
        </thead>
        <tbody>';

    $i = 1;
    foreach ($filtered as $res) {
        $html .= '<tr>
            <td>' . $i . '</td>
            <td>' . e($res['booking_code'] ?? '-') . '</td>
            <td>' . e($res['user']['name'] ?? 'Tamu') . '</td>
            <td>' . e($res['table']['restaurant']['name'] ?? '-') . '</td>
            <td>' . e($res['table']['table_number'] ?? '-') . '</td>
            <td>' . e(substr((string) ($res['reservation_date'] ?? ''), 0, 10)) . '</td>
            <td>' . e(substr((string) ($res['reservation_time'] ?? ''), 0, 5)) . '</td>
            <td>' . (int) ($res['number_of_guest'] ?? 0) . '</td>
            <td>' . e(status_humanize($res['status'] ?? '')) . '</td>
            <td>' . e(status_humanize($res['payment_status'] ?? '')) . '</td>
            <td class="right">' . fmt_rupiah($res['deposit_amount'] ?? 0) . '</td>
        </tr>';
        $i++;
    }

    $html .= '</tbody></table>';
}

$html .= '<div class="footer">Dokumen ini dibuat otomatis oleh sistem reservasi. Tanggal cetak: ' . e(date('d M Y H:i')) . '</div>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isPhpEnabled', false);
$options->set('tempDir', sys_get_temp_dir());

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$canvas = $dompdf->getCanvas();
$fontMetrics = $dompdf->getFontMetrics();
$pageWidth = $canvas->get_width();
$font = $fontMetrics->getFont('DejaVu Sans');
$canvas->page_text($pageWidth / 2 - 40, $canvas->get_height() - 18,
    'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.639, 0.561, 0.498]);

$filename = 'laporan_reservasi_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
exit;
