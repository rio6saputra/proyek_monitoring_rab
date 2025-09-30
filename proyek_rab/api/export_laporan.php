<?php
// api/export_laporan.php (Versi Final dengan Format Hierarki Lengkap)

require '../vendor/autoload.php';

include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

// =======================================================================
// FUNGSI HELPER
// =======================================================================
function build_detail_tree(array &$elements, $parentId = null) {
    $branch = [];
    foreach ($elements as $key => &$element) {
        if ($element['parent_detail_id'] == $parentId) {
            $children = build_detail_tree($elements, $element['id_rab_detail']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
            unset($elements[$key]);
        }
    }
    return $branch;
}

// =======================================================================
// 1. AMBIL DAN SIAPKAN FILTER
// =======================================================================
$biro_id = isset($_GET['biro_id']) && $_GET['biro_id'] !== 'all' ? (int)$_GET['biro_id'] : null;
$bagian_id = isset($_GET['bagian_id']) && $_GET['bagian_id'] !== 'all' ? (int)$_GET['bagian_id'] : null;
$kegiatan_id = isset($_GET['kegiatan_id']) && $_GET['kegiatan_id'] !== 'all' ? (int)$_GET['kegiatan_id'] : null;
$kro_id = isset($_GET['kro_id']) && $_GET['kro_id'] !== 'all' ? $_GET['kro_id'] : null;
$status_anggaran = isset($_GET['status_anggaran']) && $_GET['status_anggaran'] !== 'all' ? $_GET['status_anggaran'] : null;

$where_clauses = []; $params = []; $types = "";
if ($biro_id) { $where_clauses[] = "ro.biro_id = ?"; $params[] = $biro_id; $types .= "i"; }
if ($bagian_id) { $where_clauses[] = "ro.bagian_id = ?"; $params[] = $bagian_id; $types .= "i"; }
if ($kegiatan_id) { $where_clauses[] = "ro.kegiatan_id = ?"; $params[] = $kegiatan_id; $types .= "i"; }
if ($kro_id) { $where_clauses[] = "ro.kro_id = ?"; $params[] = $kro_id; $types .= "s"; }
if ($status_anggaran) { $where_clauses[] = "e.status_anggaran = ?"; $params[] = $status_anggaran; $types .= "s"; }
$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// =======================================================================
// 2. AMBIL DAN BANGUN DATA HIERARKI DARI DATABASE
// =======================================================================
$sql_data = "SELECT keg.nama_kegiatan, kro.nama_kro, kro.kro_id as kro_kode, b.nama_bagian, ro.ro_id, ro.nama_ro, ro.no_ro, komp.komponen_id, komp.kode_komponen, komp.nama_komponen, skomp.sub_komponen_id, skomp.kode_sub_komponen, skomp.nama_sub_komponen, skomp2.sub_komponen_2_id, skomp2.kode_sub_komponen_2, skomp2.nama_sub_komponen_2, e.id_rab_entry, e.status_anggaran, akun.kode_akun, akun.uraian_akun, d.* FROM rab_entry e JOIN rab_detail d ON e.id_rab_entry = d.rab_entry_id JOIN par_ro ro ON e.ro_id = ro.ro_id JOIN par_rab_bagian b ON ro.bagian_id = b.bagian_id JOIN par_rab_kegiatan keg ON ro.kegiatan_id = keg.kegiatan_id JOIN par_rab_kro kro ON ro.kro_id = kro.kro_id JOIN par_kode_akun akun ON e.id_akun = akun.id_akun LEFT JOIN par_komponen komp ON e.komponen_id = komp.komponen_id LEFT JOIN par_sub_komponen skomp ON e.sub_komponen_id = skomp.sub_komponen_id LEFT JOIN par_sub_komponen_2 skomp2 ON e.sub_komponen_2_id = skomp2.sub_komponen_2_id {$where_sql} ORDER BY ro.no_ro, komp.kode_komponen, skomp.kode_sub_komponen, skomp2.kode_sub_komponen_2, e.id_rab_entry, d.id_rab_detail";

$final_hierarchy = [];
$stmt_data = mysqli_prepare($koneksi, $sql_data);
if ($stmt_data) {
    if (!empty($params)) { mysqli_stmt_bind_param($stmt_data, $types, ...$params); }
    mysqli_stmt_execute($stmt_data);
    $result = mysqli_stmt_get_result($stmt_data);

    $hierarchy = [];
    $details_flat = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ro_id = $row['ro_id'];
        if (!isset($hierarchy[$ro_id])) { $hierarchy[$ro_id] = ['ro_id' => $ro_id, 'no_ro' => $row['no_ro'], 'nama_ro' => $row['nama_ro'], 'nama_kegiatan' => $row['nama_kegiatan'], 'nama_kro' => $row['nama_kro'], 'kro_id' => $row['kro_kode'], 'nama_bagian' => $row['nama_bagian'], 'komponens' => []]; }
        $komp_id = $row['komponen_id'];
        if ($komp_id && !isset($hierarchy[$ro_id]['komponens'][$komp_id])) { $hierarchy[$ro_id]['komponens'][$komp_id] = ['komponen_id' => $komp_id, 'kode_komponen' => $row['kode_komponen'], 'nama_komponen' => $row['nama_komponen'], 'sub_komponens' => [], 'saved_data' => []]; }
        $sub_id = $row['sub_komponen_id'];
        if ($komp_id && $sub_id && !isset($hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_id])) { $hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_id] = ['sub_komponen_id' => $sub_id, 'kode_sub_komponen' => $row['kode_sub_komponen'], 'nama_sub_komponen' => $row['nama_sub_komponen'], 'sub_komponens_2' => [], 'saved_data' => []]; }
        $sub2_id = $row['sub_komponen_2_id'];
        if ($komp_id && $sub_id && $sub2_id && !isset($hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_id]['sub_komponens_2'][$sub2_id])) { $hierarchy[$ro_id]['komponens'][$komp_id]['sub_komponens'][$sub_id]['sub_komponens_2'][$sub2_id] = ['sub_komponen_2_id' => $sub2_id, 'kode_sub_komponen_2' => $row['kode_sub_komponen_2'], 'nama_sub_komponen_2' => $row['nama_sub_komponen_2'], 'saved_data' => []]; }
        $entry_id = $row['id_rab_entry'];
        if (!isset($details_flat[$entry_id])) { $details_flat[$entry_id] = ['entry_info' => $row, 'details' => []]; }
        $details_flat[$entry_id]['details'][] = $row;
    }

    foreach ($details_flat as $entry_id => $data) {
        $entry_info = $data['entry_info'];
        $details = $data['details'];
        $detail_tree = build_detail_tree($details);
        $entry_obj = ['id_rab_entry' => $entry_id, 'kode_akun' => $entry_info['kode_akun'], 'uraian_akun' => $entry_info['uraian_akun'], 'details' => $detail_tree];
        if ($entry_info['sub_komponen_2_id']) { $hierarchy[$entry_info['ro_id']]['komponens'][$entry_info['komponen_id']]['sub_komponens'][$entry_info['sub_komponen_id']]['sub_komponens_2'][$entry_info['sub_komponen_2_id']]['saved_data'][] = $entry_obj; }
        elseif ($entry_info['sub_komponen_id']) { $hierarchy[$entry_info['ro_id']]['komponens'][$entry_info['komponen_id']]['sub_komponens'][$entry_info['sub_komponen_id']]['saved_data'][] = $entry_obj; }
        elseif ($entry_info['komponen_id']) { $hierarchy[$entry_info['ro_id']]['komponens'][$entry_info['komponen_id']]['saved_data'][] = $entry_obj; }
    }
    
    $final_hierarchy = array_values($hierarchy);
    foreach ($final_hierarchy as &$ro) {
        if(isset($ro['komponens'])) $ro['komponens'] = array_values($ro['komponens']); else $ro['komponens'] = [];
        foreach ($ro['komponens'] as &$komp) {
            if(isset($komp['sub_komponens'])) $komp['sub_komponens'] = array_values($komp['sub_komponens']); else $komp['sub_komponens'] = [];
            foreach ($komp['sub_komponens'] as &$sub) {
                if(isset($sub['sub_komponens_2'])) $sub['sub_komponens_2'] = array_values($sub['sub_komponens_2']); else $sub['sub_komponens_2'] = [];
            }
        }
    }
}

// =======================================================================
// 3. INISIALISASI DAN PENGATURAN SPREADSHEET
// =======================================================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rincian Kertas Kerja');
$headers = ['MAK', 'Keterangan Program / Kegiatan / KRO / RO / Komponen /', 'Keterangan Rincian Satuan', 'Volume', 'Harga Satuan', 'Nominal Total'];
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:F1')->getFont()->setBold(true);
$sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$rowNumber = 2;

// =======================================================================
// 4. FUNGSI UNTUK MENULIS BARIS KE EXCEL
// =======================================================================
function write_details_recursive($sheet, &$rowNumber, $details, $level) {
    $detail_indent = str_repeat('    ', $level);
    foreach ($details as $detail) {
        $volume_str = '';
        $volumes = json_decode($detail['volume_data'], true) ?? [];
        foreach($volumes as $v) { $volume_str .= ($v['volume'] ?? '') . ' ' . ($v['satuan'] ?? '') . '; '; }
        
        $sheet->setCellValue('B' . $rowNumber, $detail_indent . '- ' . $detail['uraian_pekerjaan']);
        $sheet->setCellValue('C' . $rowNumber, $detail['uraian_pekerjaan']);
        $sheet->setCellValue('D' . $rowNumber, rtrim($volume_str, '; '));
        $sheet->setCellValue('E' . $rowNumber, $detail['harga_satuan']);
        $sheet->setCellValue('F' . $rowNumber, $detail['total_biaya']);
        $rowNumber++;

        if (!empty($detail['children'])) {
            write_details_recursive($sheet, $rowNumber, $detail['children'], $level + 1);
        }
    }
}

function write_entry_to_excel($sheet, &$rowNumber, $entry, $indent_level) {
    $indent = str_repeat('    ', $indent_level);
    $sheet->setCellValue('A' . $rowNumber, $entry['kode_akun']);
    $sheet->setCellValue('B' . $rowNumber, $indent . $entry['uraian_akun']);
    $rowNumber++;
    write_details_recursive($sheet, $rowNumber, $entry['details'], $indent_level + 1);
}

// =======================================================================
// 5. TULIS DATA HIERARKI DAN KIRIM FILE
// =======================================================================
foreach ($final_hierarchy as $ro) {
    $sheet->setCellValue('A' . $rowNumber, $ro['no_ro'])->getStyle('A'.$rowNumber)->getFont()->setBold(true);
    $sheet->setCellValue('B' . $rowNumber, $ro['nama_ro'])->getStyle('B'.$rowNumber)->getFont()->setBold(true);
    $rowNumber++;

    foreach ($ro['komponens'] as $komp) {
        $sheet->setCellValue('A' . $rowNumber, $komp['kode_komponen'])->getStyle('A'.$rowNumber)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $rowNumber, '    ' . $komp['nama_komponen'])->getStyle('B'.$rowNumber)->getFont()->setBold(true);
        $rowNumber++;
        foreach ($komp['saved_data'] as $entry) { write_entry_to_excel($sheet, $rowNumber, $entry, 2); }

        foreach ($komp['sub_komponens'] as $sub) {
            $sheet->setCellValue('A' . $rowNumber, $sub['kode_sub_komponen'])->getStyle('A'.$rowNumber)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $rowNumber, '        ' . $sub['nama_sub_komponen'])->getStyle('B'.$rowNumber)->getFont()->setBold(true);
            $rowNumber++;
            foreach ($sub['saved_data'] as $entry) { write_entry_to_excel($sheet, $rowNumber, $entry, 3); }

            foreach ($sub['sub_komponens_2'] as $sub2) {
                $sheet->setCellValue('A' . $rowNumber, $sub2['kode_sub_komponen_2'])->getStyle('A'.$rowNumber)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $rowNumber, '            ' . $sub2['nama_sub_komponen_2'])->getStyle('B'.$rowNumber)->getFont()->setBold(true);
                $rowNumber++;
                foreach ($sub2['saved_data'] as $entry) { write_entry_to_excel($sheet, $rowNumber, $entry, 4); }
            }
        }
    }
}

// Atur style akhir
$sheet->getStyle('E:F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
foreach (range('A', 'F') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}
$sheet->getColumnDimension('B')->setWidth(60);

$writer = new Xlsx($spreadsheet);
$fileName = 'Laporan_RAB_Hierarki_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');

mysqli_close($koneksi);
exit;
?>