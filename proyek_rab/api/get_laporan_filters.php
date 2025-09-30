<?php
// api/get_laporan_filters.php (Versi Final dengan Hak Akses Penuh & Keamanan)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$response = [
    'biro' => [], 'bagian' => [], 'kegiatan' => [],
    'kro' => [], 'status' => []
];

$user_role = $_SESSION['user_role'] ?? 'Guest';
$user_bagian_id = $_SESSION['bagian_id'] ?? null;

try {
    $where_clause = '';
    $params = [];
    $types = '';

    // Terapkan filter hak akses HANYA untuk non-admin
    if ($user_role !== 'admin' && $user_bagian_id) {
        $where_clause = "WHERE ro.bagian_id = ?";
        $params[] = $user_bagian_id;
        $types .= "i";
    }

    // Fungsi untuk menjalankan query dengan aman
    function execute_query($koneksi, $sql, $params, $types) {
        $data = [];
        $stmt = mysqli_prepare($koneksi, $sql);
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
        return $data;
    }

    // Ambil data Biro
    $sql_biro = "SELECT DISTINCT b.biro_id, b.nama_biro FROM par_rab_biro b JOIN par_ro ro ON b.biro_id = ro.biro_id JOIN rab_entry e ON ro.ro_id = e.ro_id {$where_clause} ORDER BY b.nama_biro";
    $response['biro'] = execute_query($koneksi, $sql_biro, $params, $types);

    // Ambil data Bagian
    $sql_bagian = "SELECT DISTINCT bg.bagian_id, bg.nama_bagian, bg.biro_id FROM par_rab_bagian bg JOIN par_ro ro ON bg.bagian_id = ro.bagian_id JOIN rab_entry e ON ro.ro_id = e.ro_id {$where_clause} ORDER BY bg.nama_bagian";
    $response['bagian'] = execute_query($koneksi, $sql_bagian, $params, $types);

    // Ambil data Kegiatan
    $sql_kegiatan = "SELECT DISTINCT keg.kegiatan_id, keg.nama_kegiatan FROM par_rab_kegiatan keg JOIN par_ro ro ON keg.kegiatan_id = ro.kegiatan_id JOIN rab_entry e ON ro.ro_id = e.ro_id {$where_clause} ORDER BY keg.nama_kegiatan";
    $response['kegiatan'] = execute_query($koneksi, $sql_kegiatan, $params, $types);

    // Ambil data KRO
    $sql_kro = "SELECT DISTINCT kro.kro_id, kro.nama_kro FROM par_rab_kro kro JOIN par_ro ro ON kro.kro_id = ro.kro_id JOIN rab_entry e ON ro.ro_id = e.ro_id {$where_clause} ORDER BY kro.nama_kro";
    $response['kro'] = execute_query($koneksi, $sql_kro, $params, $types);

    // Ambil data Status (tidak perlu filter hak akses)
    $sql_status = "SELECT DISTINCT status_anggaran FROM RAB_Entry ORDER BY status_anggaran";
    $status_data = execute_query($koneksi, $sql_status, [], '');
    foreach ($status_data as $row) {
        $response['status'][] = ['value' => $row['status_anggaran'], 'text' => ucwords(strtolower(str_replace('_', ' ', $row['status_anggaran'])))];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
}

mysqli_close($koneksi);
?>