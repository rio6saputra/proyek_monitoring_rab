-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Sep 2025 pada 04.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erab_setjen_dpd`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashed_validator` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `selector`, `hashed_validator`, `user_id`, `expires`) VALUES
(5, 'f9cc6b4063b21cc12364fd972c27307a', '$2y$10$P871A6VPWUTvVMqspKShL.PE65958CqotQ4TT2SaQZ.vWbH/bcp0K', 25, '2025-10-29 02:46:15'),
(6, 'f17e2529c7d9a7a24fdc8aa05eda374e', '$2y$10$uYNW50qbddMN457RHGHv8uIs6we62PSCpoJhPVX4ycu0ch7VTY9xG', 1, '2025-10-29 03:56:17'),
(7, '9f4964d8d3735b63df26e10f48c4a964', '$2y$10$595vsUuaZspI/cqXPGw5Me/PvT6YujMRPiuM6fCaF5UZmsJjANgUa', 20, '2025-10-29 10:51:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_kode_akun`
--

CREATE TABLE `par_kode_akun` (
  `id_akun` int(11) NOT NULL,
  `kode_akun` varchar(20) NOT NULL,
  `uraian_akun` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_kode_akun`
--

INSERT INTO `par_kode_akun` (`id_akun`, `kode_akun`, `uraian_akun`) VALUES
(1, '521211', 'Belanja Bahan'),
(2, '521213', 'Belanja Honor Output Kegiatan'),
(3, '521219', 'Belanja Barang Non Operasional Lainnya'),
(4, '522131', 'Belanja Jasa Konsultan'),
(5, '522141', 'Belanja Sewa'),
(6, '522151', 'Belanja Jasa Profesi'),
(7, '524111', 'Belanja Perjalanan Dinas Biasa'),
(8, '524113', 'Belanja Perjalanan Dinas Dalam Kota'),
(9, '524114', 'Belanja Perjalanan Dinas Paket Meeting Dalam Kota'),
(10, '524119', 'Belanja Perjalanan Dinas Paket Meeting Luar Kota'),
(11, '524219', 'Belanja Perjalanan Dinas Lainnya - Luar Negeri');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_komponen`
--

CREATE TABLE `par_komponen` (
  `komponen_id` varchar(100) NOT NULL,
  `ro_id` varchar(50) NOT NULL,
  `kode_komponen` varchar(10) NOT NULL,
  `nama_komponen` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_komponen`
--

INSERT INTO `par_komponen` (`komponen_id`, `ro_id`, `kode_komponen`, `nama_komponen`) VALUES
('7983.AAA.001.051', '7983.AAA.001', '051', 'PENYUSUNAN NASKAH AKADEMIK DAN DRAFTING RUU'),
('7983.AAA.001.052', '7983.AAA.001', '052', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI'),
('7983.AAA.001.053', '7983.AAA.001', '053', 'STUDI BANDING RUU'),
('7983.AAA.001.054', '7983.AAA.001', '054', 'UJI SAHIH RUU'),
('7983.AAA.001.055', '7983.AAA.001', '055', 'FINALISASI DRAFT RUU USUL DPD'),
('7983.AAA.002.051', '7983.AAA.002', '051', 'PENYUSUNAN NASKAH AKADEMIK DAN DRAFTING RUU'),
('7983.AAA.002.052', '7983.AAA.002', '052', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI'),
('7983.AAA.002.053', '7983.AAA.002', '053', 'STUDI BANDING RUU'),
('7983.AAA.002.054', '7983.AAA.002', '054', 'UJI SAHIH RUU'),
('7983.AAA.002.055', '7983.AAA.002', '055', 'FINALISASI DRAFT RUU USUL DPD'),
('7983.AAA.003.051', '7983.AAA.003', '051', 'PENYUSUNAN NASKAH AKADEMIK DAN DRAFTING RUU'),
('7983.AAA.003.052', '7983.AAA.003', '052', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI'),
('7983.AAA.003.053', '7983.AAA.003', '053', 'STUDI BANDING RUU'),
('7983.AAA.003.054', '7983.AAA.003', '054', 'UJI SAHIH RUU'),
('7983.AAA.003.055', '7983.AAA.003', '055', 'FINALISASI DRAFT RUU USUL TUGAS KOMITE III DPD'),
('7983.AAA.004.051', '7983.AAA.004', '051', 'PENYUSUNAN NASKAH AKADEMIK DAN DRAF RUU USUL INISIATIF DPD'),
('7983.AAA.004.052', '7983.AAA.004', '052', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI RUU'),
('7983.AAA.004.053', '7983.AAA.004', '053', 'STUDI BANDING RUU'),
('7983.AAA.004.054', '7983.AAA.004', '054', 'UJI SAHIH RUU'),
('7983.AAA.004.055', '7983.AAA.004', '055', 'FINALISASI DRAFT RUU USUL INISIATIF DPD'),
('7983.AAA.005.051', '7983.AAA.005', '051', 'PENYUSUNAN NASKAH AKADEMIK DAN DRAFTING RUU'),
('7983.AAA.005.052', '7983.AAA.005', '052', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI RUU'),
('7983.AAA.005.053', '7983.AAA.005', '053', 'STUDI BANDING RUU'),
('7983.AAA.005.054', '7983.AAA.005', '054', 'UJI SAHIH RUU'),
('7983.AAA.005.055', '7983.AAA.005', '055', 'FINALISASI DRAFT RUU USUL DPD'),
('7983.AAA.006.051', '7983.AAA.006', '051', 'HARMONISASI PEMBULATAN DAN PEMANTAPAN KONSEPSI RUU'),
('7983.AAA.006.052', '7983.AAA.006', '052', 'PENETAPAN RUU USUL DARI DPD RI'),
('7983.AAA.007.051', '7983.AAA.007', '051', 'PENYUSUNAN MATERI RUU HASIL PERTIMBANGAN DPD RI'),
('7983.AAA.007.052', '7983.AAA.007', '052', 'FINALISASI PENYUSUNAN RUU LAINNYA HASIL PERTIMBANGAN DPD RI'),
('7983.AAA.008.051', '7983.AAA.008', '051', 'PENYUSUNAN NASKAH AKADEMIK USUL PROLEGNAS DPD RI'),
('7983.AAA.008.052', '7983.AAA.008', '052', 'PEMBAHASAN BERSAMA DPR DAN PRESIDEN (PEMERINTAH) LINGKUP KERJA PPUU'),
('7983.AAA.008.053', '7983.AAA.008', '053', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI USUL PROLEGNAS DPD'),
('7983.AAA.008.054', '7983.AAA.008', '054', 'FINALISASI USUL PROLEGNAS DPD RI'),
('7983.AAA.009.051', '7983.AAA.009', '051', 'PENYUSUNAN PANDANGAN/PENDAPAT DAN PERTIMBANGAN DPD'),
('7983.AAA.009.052', '7983.AAA.009', '052', 'PEMBAHASAN BERSAMA DPR DAN PRESIDEN (PEMERINTAH) LINGKUP KERJA KOMITE I'),
('7983.AAA.009.053', '7983.AAA.009', '053', 'PENYELENGGARAAN PENYUSUNAN PENDAPAT/PERTIMBANGAN DPD TENTANG PENILAIAN PERSYARATAN ADMINISTRATIF DAERAH OTONOMI BARU'),
('7983.AAA.009.054', '7983.AAA.009', '054', 'PENYELENGGARAAN PENYUSUNAN PENDAPAT/PERTIMBANGAN DPD TENTANG PENILAIAN PERSYARATAN DASAR KEWILAYAHAN DAERAH OTONOMI BARU'),
('7983.AAA.009.055', '7983.AAA.009', '055', 'PENYUSUNAN PENDAPAT/PERTIMBANGAN DPD TENTANG PELAKSANAAN UU TENTANG DESA'),
('7983.AAA.010.051', '7983.AAA.010', '051', 'KUNJUNGAN KERJA DALAM RANGKAPENYUSUNAN PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE II DPD'),
('7983.AAA.010.052', '7983.AAA.010', '052', 'FINALISASI PENYUSUNAN PANDANGAN DAN PENDAPATBIDANG TUGAS KOMITE II'),
('7983.AAA.010.053', '7983.AAA.010', '053', 'PEMBAHASAN BERSAMA DPR DAN PRESIDEN (PEMERINTAH) LINGKUP KERJA KOMITE II'),
('7983.AAA.010.054', '7983.AAA.010', '054', 'PENYELENGGARAAN PENYUSUNAN DAFTAR INVENTARISASI MASALAH (DIM) RUU TERTENTU DARI DPR DAN PEMERINTAH'),
('7983.AAA.011.051', '7983.AAA.011', '051', 'PENYUSUNAN RUU HASIL PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE III'),
('7983.AAA.011.052', '7983.AAA.011', '052', 'PEMBAHASAN BERSAMA DPR DAN PRESIDEN (PEMERINTAH) LINGKUP KERJA KOMITE III'),
('7983.AAA.011.053', '7983.AAA.011', '053', 'Finalisasi Penyusunan Pandangan/Pendapat Bidang Tugas Komite III'),
('7983.AAA.012.051', '7983.AAA.012', '051', 'INVENTARISASI PENYUSUNAN PANDANGAN/PENDAPAT DPD RI ATAS RUU NON APBN/APBNP'),
('7983.AAA.012.052', '7983.AAA.012', '052', 'FINALISASI PENYUSUNAN PANDANGAN/PENDAPAT DPD RI ATAS RUU NON APBN/APBNP'),
('7983.AAA.012.053', '7983.AAA.012', '053', 'PEMBAHASAN BERSAMA DPR DAN PRESIDEN (PEMERINTAH) LINGKUP KERJA KOMITE IV'),
('7983.AAA.013.051', '7983.AAA.013', '051', 'PENYUSUNAN PERTIMBANGAN DPD RI ATAS RUU APBN/APBNP DAN RUU PERTANGGUNGJAWABAN PELAKSANAAN APBN'),
('7983.AAA.013.052', '7983.AAA.013', '052', 'PENYELENGGARAAN PENYUSUNAN PERTIMBANGAN DPD RUU TENTANG APBN DAN RUU YANG BERKAITAN DENGAN PAJAK'),
('7983.AAA.014.051', '7983.AAA.014', '051', 'PENYUSUNAN PERTIMBANGAN DPD RI ATAS RUU PAJAK'),
('7983.AAA.014.052', '7983.AAA.014', '052', 'FINALISASI PENYUSUNAN PERTIMBANGAN DPD RI ATAS RUU PAJAK'),
('7983.ABA.001.051', '7983.ABA.001', '051', 'PENYUSUNAN REKOMENDASI KEBIJAKAN TERKAIT RENCANA KERJA PEMERINTAH'),
('7983.ABA.001.055', '7983.ABA.001', '055', 'FINALISASI DRAFT REKOMENDASI KEBIJAKAN TERKAIT RENCANA KERJA PEMERINTAH'),
('7983.ABC.001.051', '7983.ABC.001', '051', 'PENGOLAHAN DATA ASMASDA'),
('7983.ABC.001.052', '7983.ABC.001', '052', 'PENYUSUNAN MATERI DAN TINDAK LANJUT PENYERAPAN ASPIRASI MASYARAKAT DAN DAERAH'),
('7983.ABC.001.053', '7983.ABC.001', '053', 'PENYUSUNAN MEDIANUSA'),
('7983.ABC.002.051', '7983.ABC.002', '051', 'INVENTARISASI MATERI/PENGUMPULAN DATA/STUDI PENDALAMAN'),
('7983.ABC.002.052', '7983.ABC.002', '052', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7983.ABC.002.053', '7983.ABC.002', '053', 'FINALISASI MATERI'),
('7983.ABC.002.054', '7983.ABC.002', '054', 'DUKUNGAN KEAHLIAN PENYUSUNAN RUU USUL/PROLEGNAS DI KOMITE DAN PPUU'),
('7983.ABC.003.051', '7983.ABC.003', '051', 'INVENTARISASI MATERI/PENGUMPULAN DATA/STUDI PENDALAMAN'),
('7983.ABC.003.052', '7983.ABC.003', '052', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7983.ABC.003.053', '7983.ABC.003', '053', 'FINALISASI MATERI'),
('7983.ABC.004.051', '7983.ABC.004', '051', 'INVENTARISASI MATERI/PENGUMPULAN DATA/STUDI PENDALAMAN'),
('7983.ABC.004.052', '7983.ABC.004', '052', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7983.ABC.004.053', '7983.ABC.004', '053', 'FINALISASI MATERI'),
('7983.AΕA.001.051', '7983.AΕA.001', '051', 'PENERIMAAN DAN PENGUMPULAN ASMASDA'),
('7983.AΕA.001.052', '7983.AΕA.001', '052', 'PENGOLAHAN ASMASDA'),
('7983.AΕA.001.053', '7983.AΕA.001', '053', 'TINDAK LANJUT HASIL PENGOLAHAN ASMASDA'),
('7983.BMB.001.051', '7983.BMB.001', '051', 'DUKUNGAN KEGIATAN DAN PERJALANAN DINAS'),
('7983.BMB.002.051', '7983.BMB.002', '051', 'DUKUNGAN KEGIATAN DAN PERJALANAN DINAS'),
('7983.PBC.001.051', '7983.PBC.001', '051', 'PENGOLAHAN DATA ASMASDA'),
('7983.PBC.001.052', '7983.PBC.001', '052', 'Focus Group Disscussion'),
('7983.PBC.001.053', '7983.PBC.001', '053', 'Penyusunan Laporan Akhir'),
('7983.PBC.001.054', '7983.PBC.001', '054', 'STAKEHOLDER ENGAGEMENT'),
('7983.PBC.002.051', '7983.PBC.002', '051', 'PENGOLAHAN DATA ASMASDA'),
('7983.PBC.002.052', '7983.PBC.002', '052', 'Focus Group Disscussion'),
('7983.PBC.002.053', '7983.PBC.002', '053', 'Penyusunan Laporan Akhir'),
('7983.PBC.002.054', '7983.PBC.002', '054', 'STAKEHOLDER ENGAGEMENT'),
('7983.PBC.003.051', '7983.PBC.003', '051', 'Sinkronisasi Data'),
('7983.PBC.003.052', '7983.PBC.003', '052', 'Focus Group Discussion'),
('7983.PBC.003.053', '7983.PBC.003', '053', 'Finalisasi'),
('7983.PBC.004.051', '7983.PBC.004', '051', 'PENGOLAHAN DATA ASMASDA'),
('7983.PBC.004.052', '7983.PBC.004', '052', 'Focus Group Disscussion'),
('7983.PBC.004.053', '7983.PBC.004', '053', 'Penyusunan Laporan Akhir'),
('7983.PBC.004.054', '7983.PBC.004', '054', 'STAKEHOLDER ENGAGEMENT'),
('7984.ABA.001.051', '7984.ABA.001', '051', 'TINDAK LANJUT ATAS HASIL PEMERIKSAAN KEUANGAN NEGARA (IHPS BPK)'),
('7984.ABA.001.052', '7984.ABA.001', '052', 'PENYELENGGARAAN PENYUSUNAN PERTIMBANGAN DPD TERHADAP TINDAK LANJUT HASIL PEMERIKSAAN SEMESTERAN (HAPSEM) BPK'),
('7984.ABA.001.053', '7984.ABA.001', '053', 'FINALISASI PENYUSUNAN PERTIMBANGAN DPD ATAS IHPS BPK'),
('7984.ABC.001.051', '7984.ABC.001', '051', 'INVENTARISASI MATERI PENGAWASAN ATAS PELAKSANAAN UU'),
('7984.ABC.001.052', '7984.ABC.001', '052', 'PENYUSUNAN HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.001.053', '7984.ABC.001', '053', 'PENYELENGGARAAN PEMBINAAN'),
('7984.ABC.001.054', '7984.ABC.001', '054', 'PENYUSUNAN HASIL PENGAWASAN DPD TENTANG KEBIJAKAN DAU TENTANG DANA DESA'),
('7984.ABC.001.055', '7984.ABC.001', '055', 'PENYUSUNAN LAPORAN KINERJA KOMITE I'),
('7984.ABC.002.051', '7984.ABC.002', '051', 'KUNJUNGAN KERJA DALAM RANGKA INVENTARISASI MATERI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.002.052', '7984.ABC.002', '052', 'FINALISASI PENYUSUNAN HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.002.053', '7984.ABC.002', '053', 'PENYELENGGARAAN PENYUSUNAN HASIL PENGAWASAN DPD TENTANG PELAKSANAAN UU TERTENTU DI DAERAH'),
('7984.ABC.002.054', '7984.ABC.002', '054', 'PENYUSUNAN LAPORAN KINERJA KOMITE II'),
('7984.ABC.003.051', '7984.ABC.003', '051', 'KUNJUNGAN KERJA INVENTARISASI MATERI'),
('7984.ABC.003.052', '7984.ABC.003', '052', 'PELAKSANAAN PENGAWASAN UU NO. 8 TAHUN 2019 TENTANG PENYELENGGARAAN IBADAH HAJI DAN UMROH'),
('7984.ABC.003.053', '7984.ABC.003', '053', 'PELAKSANAAN PENGAWASAN UU NO.18 TAHUN 2017 TENTANG PELINDUNGAN PEKERJA MIGRAN INDONESIA'),
('7984.ABC.003.054', '7984.ABC.003', '054', 'PENYUSUNAN HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.003.055', '7984.ABC.003', '055', 'FINALISASI PENYUSUNAN HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.003.056', '7984.ABC.003', '056', 'PENYUSUNAN LAPORAN KINERJA KOMITE III'),
('7984.ABC.004.051', '7984.ABC.004', '051', 'INVENTARISASI MATERI'),
('7984.ABC.004.052', '7984.ABC.004', '052', 'FINALISASI PENYUSUNAN HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU'),
('7984.ABC.004.053', '7984.ABC.004', '053', 'PENYUSUNAN BUKU LAPORAN KINERJA KOMITE IV'),
('7984.ABC.005.051', '7984.ABC.005', '051', 'DUKUNGAN KEAHLIAN DAN ASISTENSI'),
('7984.ABC.005.052', '7984.ABC.005', '052', 'FIT AND PROPER TEST PIMPINAN/ANGGOTA BPK'),
('7984.ABC.006.051', '7984.ABC.006', '051', 'PENYUSUNAN PROGRAM PEMANTAUAN DAN EVALUASI RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH'),
('7984.ABC.006.052', '7984.ABC.006', '052', 'PEMANTAUAN RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH'),
('7984.ABC.006.053', '7984.ABC.006', '053', 'EVALUASI HASIL PEMANTAUAN RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH'),
('7984.ABC.006.054', '7984.ABC.006', '054', 'PERUMUSAN HASIL PEMANTAUAN DAN EVALUASI RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH'),
('7984.ABC.006.055', '7984.ABC.006', '055', 'KONSULTASI PUBLIK'),
('7984.ABC.006.056', '7984.ABC.006', '056', 'PENYUSUNAN HASIL PEMANTAUAN DAN EVALUASI RANCANGAN PERATURAN DAERAH / PERATURAN DAERAH'),
('7984.ABC.006.057', '7984.ABC.006', '057', 'PENYUSUNAN MATERI KINERJA BULD'),
('7984.ABC.007.051', '7984.ABC.007', '051', 'KONSULTASI STAKEHOLDERS DAERAH'),
('7984.ABC.007.052', '7984.ABC.007', '052', 'TEMU KONSULTASI LEGISLASI PUSAT-DAERAH'),
('7984.ABC.007.053', '7984.ABC.007', '053', 'PERUMUSAN'),
('7984.ABC.007.054', '7984.ABC.007', '054', 'FINALISASI'),
('7984.ABC.008.051', '7984.ABC.008', '051', 'PERENCANAAN'),
('7984.ABC.008.052', '7984.ABC.008', '052', 'PELAKSANAAN'),
('7984.ABC.009.051', '7984.ABC.009', '051', 'PELAKSANAAN'),
('7984.ABC.009.052', '7984.ABC.009', '052', 'PENYUSUNAN REKOMENDASI ATAS HASIL PENINJAUAN PELAKSANAAN UU'),
('7984.ABC.009.053', '7984.ABC.009', '053', 'PENYUSUNAN LAPORAN KINERJA PPUU'),
('7984.ABC.010.051', '7984.ABC.010', '051', 'MENGHADIRI KEGIATAN SEMINAR/WORKSHOP ATAU PENYELESAIAN DOKUMEN'),
('7984.ABC.010.052', '7984.ABC.010', '052', 'INVENTARISASI MATERI/PENGUMPULAN DATA/ STUDI PENDALAMAN'),
('7984.ABC.010.053', '7984.ABC.010', '053', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7984.ABC.010.054', '7984.ABC.010', '054', 'FINALISASI MATERI'),
('7984.ABC.010.055', '7984.ABC.010', '055', 'DUKUNGAN LITIGASI BAGI DPD RI'),
('7984.ABC.011.051', '7984.ABC.011', '051', 'INVENTARISASI MATERI/PENGUMPULAN DATA/STUDI PENDALAMAN'),
('7984.ABC.011.052', '7984.ABC.011', '052', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7984.ABC.011.053', '7984.ABC.011', '053', 'FINALISASI MATERI'),
('7984.ABC.012.051', '7984.ABC.012', '051', 'INVENTARISASI MATERI/PENGUMPULAN DATA/STUDI PENDALAMAN'),
('7984.ABC.012.052', '7984.ABC.012', '052', 'RAPAT KONSOLIDASI PUSAT PERANCANGAN DAN KAJIAN KEBIJAKAN HUKUM'),
('7984.ABC.012.053', '7984.ABC.012', '053', 'DUKUNGAN KEAHLIAN PENYUSUNAN HASIL PENGAWASAN UU DI KOMITE DPD RI'),
('7984.ABC.012.054', '7984.ABC.012', '054', 'PEER REVIEW KAJIAN DENGAN PAKAR/AHLI'),
('7984.ABC.012.055', '7984.ABC.012', '055', 'FINALISASI MATERI'),
('7984.ABC.013.051', '7984.ABC.013', '051', 'PEMUTAKHIRAN INFORMASI'),
('7984.ABC.013.052', '7984.ABC.013', '052', 'TINJAUAN LAPANGAN'),
('7984.ABC.013.053', '7984.ABC.013', '053', 'DIALOG PUBLIK'),
('7984.ABC.013.054', '7984.ABC.013', '054', 'PERUMUSAN MATERI'),
('7984.ABC.013.055', '7984.ABC.013', '055', 'DISEMINASI'),
('7984.ABC.013.056', '7984.ABC.013', '056', 'PENYUSUNAN LAPORAN'),
('7984.ABC.013.057', '7984.ABC.013', '057', 'FINALISASI'),
('7984.AΕA.001.051', '7984.AΕA.001', '051', 'PELAKSANAAN FUNGSI DUKUNGAN PENYUSUNAN MATERI DAN PERSIDANGAN DALAM LINGKUP BIRO PERSIDANGAN I'),
('7984.AΕA.002.051', '7984.AΕA.002', '051', 'PELAKSANAAN FUNGSI DUKUNGAN PENYUSUNAN MATERI DAN PERSIDANGAN DALAM LINGKUP BIRO PERSIDANGAN II'),
('7984.AΕA.003.051', '7984.AΕA.003', '051', 'KUNJUNGAN KERJA'),
('7984.AΕA.004.051', '7984.AΕA.004', '051', 'KUNJUNGAN KERJA'),
('7984.AΕA.005.051', '7984.AΕA.005', '051', 'KUNJUNGAN KERJA'),
('7984.AΕA.006.051', '7984.AΕA.006', '051', 'KUNJUNGAN KERJA'),
('7984.PBC.001.051', '7984.PBC.001', '051', 'TELAAH ASMASDA'),
('7984.PBC.001.052', '7984.PBC.001', '052', 'PENETAPAN PRIORITAS PENGAWASAN ATAS ASMASDA PELAKSANAAN UU TERTENTU'),
('7984.PBC.002.051', '7984.PBC.002', '051', 'TELAHAAN ASMASDA'),
('7984.PBC.002.052', '7984.PBC.002', '052', 'PENETAPAN PRIORITAS PENGAWASAN ATAS ASMASDA PELAKSANAAN UU TERTENTU'),
('7985.AAH.001.051', '7985.AAH.001', '051', 'PENYUSUNAN DAN PEMBAHASAN'),
('7985.AAH.002.051', '7985.AAH.002', '051', 'PENCEGAHAN DAN PENANGANGAN'),
('7985.AAH.003.051', '7985.AAH.003', '051', 'EVALUASI DAN PENYEMPURNAAN PERATURAN DPD TENTANG TATA TERTIB/KODE ETIK/TATA BERACARA BADAN KEHORMATAN'),
('7985.ABA.001.051', '7985.ABA.001', '051', 'TINDAK LANJUT HASIL PEMERIKSAAN BPK YANG BERINDIKASI KERUGIAN NEGARA/DAERAH'),
('7985.ABA.001.052', '7985.ABA.001', '052', 'FINALISASI REKOMENDASI'),
('7985.ABC.001.051', '7985.ABC.001', '051', 'TINDAK LANJUT PENANGANAN PENGADUAN MASYARAKAT DAN PERMASALAHAN YANG DISAMPAIKAN PEMERINTAH DAERAH'),
('7985.ABC.001.052', '7985.ABC.001', '052', 'FINALISASI REKOMENDASI'),
('7985.ABC.001.053', '7985.ABC.001', '053', 'PENYUSUNAN LAPORAN KINERJA HASIL TINDAK LANJUT PENGADUAN MASYARAKAT DAN/ATAU IHPS'),
('7985.ABL.001.051', '7985.ABL.001', '051', 'PERSIAPAN'),
('7985.ABL.001.052', '7985.ABL.001', '052', 'PELAKSANAAN'),
('7985.ABL.001.053', '7985.ABL.001', '053', 'PENYUSUNAN LAPORAN KINERJA PURT'),
('7985.AEC.001.051', '7985.AEC.001', '051', 'KERJA SAMA BILATERAL/MULTILATERAL PARLEMEN'),
('7985.AEC.001.052', '7985.AEC.001', '052', 'FASILITASI KERJA SAMA DAERAH DENGAN LUAR NEGERI'),
('7985.AEC.001.053', '7985.AEC.001', '053', 'PENYELENGGARAAN PENYUSUNAN MATERI DPD PADA KEGIATAN DIPLOMASI INTERNASIONAL BERSIFAT KEDAERAHAN'),
('7985.AΕA.001.051', '7985.AΕA.001', '051', 'PELAKSANAAN TUGAS DAN KEWENANGAN PANMUS DPD RI'),
('7985.AΕA.001.052', '7985.AΕA.001', '052', 'PANSUS DPD RI'),
('7985.BLA.001.051', '7985.BLA.001', '051', 'SIDANG PARIPURNA MENDENGARKAN PIDATO KENEGARAAN PRESIDEN RI'),
('7986.AΕA.001.051', '7986.AΕA.001', '051', 'PELAKSANAAN DAN PEMASYARAKATAN KEPUTUSAN DPD RI'),
('7986.AΕA.001.052', '7986.AΕA.001', '052', 'KESEKRETARIATAN PIMPINAN DPD RI DAN PIMPINAN SEKRETARIAT JENDERAL DPD RI'),
('7986.AΕA.001.053', '7986.AΕA.001', '053', 'FASILITASI KEGIATAN PENERIMAAN KUNJUNGAN STAKEHOLDER/KONSTITUEN'),
('7986.AΕA.001.054', '7986.AΕA.001', '054', 'KOORDINASI/KONSULTASI PIMPINAN DPD RI'),
('7986.AΕA.001.055', '7986.AΕA.001', '055', 'KUNJUNGAN KERJA DALAM RANGKA TUGAS MENDESAK'),
('7986.AΕA.001.056', '7986.AΕA.001', '056', 'PENERIMAAN KUNJUNGAN BALASAN DELEGASI LUAR NEGERI'),
('7986.BMA.001.051', '7986.BMA.001', '051', 'KOORDINASI DAN KONSULTASI JDIH'),
('7986.BMA.001.052', '7986.BMA.001', '052', 'MONOGRAFI HUKUM JDIH DPD');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_rab_bagian`
--

CREATE TABLE `par_rab_bagian` (
  `bagian_id` int(11) NOT NULL,
  `biro_id` int(11) NOT NULL,
  `nama_bagian` varchar(255) NOT NULL,
  `bagian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_rab_bagian`
--

INSERT INTO `par_rab_bagian` (`bagian_id`, `biro_id`, `nama_bagian`, `bagian`) VALUES
(1, 1, 'bagian organisasi dan ketatalaksanaan', 'ortala'),
(2, 1, 'bagian administrasi keanggotaan dan kepegawaian', 'AKK'),
(3, 1, 'Bagian Pengembangan Sumber Daya Manusia', 'psdm'),
(4, 1, 'bagian hukum', 'hukum'),
(5, 2, 'bagian perencanaan', 'perencanaan'),
(6, 2, 'bagian administrasi gaji, tunjangan dan honorarium', 'gaji'),
(7, 2, 'bagian perbendaharaan', 'perben'),
(8, 2, 'bagian akuntansi dan pelaporan', 'aklap'),
(9, 3, 'bagian pengelolaan sistem informasi', 'bpsi'),
(10, 3, 'bagian risalah', 'risalah'),
(11, 3, 'bagian kearsipan, perpustakaan, dan penerbitan', 'kpp'),
(12, 4, 'bagian pengelolaan barang milik negara', 'pbmn'),
(13, 4, 'bagian pemeliharaan dan perlengkapan', 'pemeliharaan'),
(14, 4, 'bagian layanan pengadaan', 'layanan'),
(15, 4, 'bagian pengamanan dalam', 'pamdal'),
(16, 5, 'bagian protokol', 'protokol'),
(17, 5, 'bagian hubungan masyarakat dan fasilitasi pengaduan', 'humas'),
(18, 5, 'bagian pemberitaan dan media', 'media'),
(19, 6, 'bagian sekretariat komite I', 'komite_1'),
(20, 6, 'bagian sekretariat komite III', 'komite_3'),
(21, 6, 'bagian sekretariat panitia perancang undang-undang', 'ppuu'),
(22, 6, 'bagian sekretariat badan urusan legislasi daerah', 'buld'),
(23, 6, 'bagian sekretariat badan kerja sama parlemen', 'bksp'),
(24, 7, 'bagian sekretariat komite II', 'komite_2'),
(25, 7, 'bagian sekretariat komite IV', 'komite_4'),
(26, 7, 'bagaian sekretariat persidangan paripurna/panmus/pansus', 'panmus'),
(27, 7, 'bagian sekretariat badan kehormatan', 'bk'),
(28, 7, 'bagian sekretariat panitia urusan rumah tangga', 'purt'),
(29, 7, 'bagian sekretariat badan akuntabilitas publik', 'bap'),
(30, 8, 'bagian sekretariat ketua DPD RI', 'set_ketua'),
(31, 8, 'bagian sekretariat wakil ketua DPD RI bidang I', 'set_waka_1'),
(32, 8, 'bagian sekretariat wakil ketua DPD RI bidang II', 'set_waka_2'),
(33, 8, 'bagian sekretariat wakil ketua DPD RI bidang III', 'set_waka_3'),
(34, 8, 'bagian tata usaha pimpinan sekretariat jenderal', 'tu_setpim'),
(35, 9, 'bidang perancang dan pemantauan peraturan perundang-undangan', 'perancang'),
(36, 9, 'bidang dokumentasi dan jaringan informasi hukum pusat dan daerah', 'jdih'),
(37, 9, 'subbagian TU pusperjakum', 'tu_pusperjakum'),
(38, 10, 'bidang diseminasi aspirasi masyarakat dan daerah', 'asmasda'),
(39, 10, 'bidang pengkajian dan informasi anggaran pusat dan daerah', 'agpusda'),
(40, 10, 'subbagian TU puskadaran', 'tu_puskadaran'),
(41, 6, 'Bagian Materi Persidangan I', 'tu_rosid_1'),
(42, 7, 'Bagian Materi Persidangan II', 'tu_rosid_2');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_rab_biro`
--

CREATE TABLE `par_rab_biro` (
  `biro_id` int(11) NOT NULL,
  `nama_biro` varchar(255) NOT NULL,
  `biro` varchar(100) DEFAULT NULL,
  `deputi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_rab_biro`
--

INSERT INTO `par_rab_biro` (`biro_id`, `nama_biro`, `biro`, `deputi`) VALUES
(1, 'Biro Organisasi, Keanggotaan, dan Kepegawaian', 'biro_okk', 'administrasi'),
(2, 'Biro Perencanaan dan Keuangan', 'biro_renkeu', 'administrasi'),
(3, 'Biro Sistem Informasi dan Dokumentasi', 'biro_sindok', 'administrasi'),
(4, 'Biro Umum', 'biro_umum', 'administrasi'),
(5, 'Biro Protokol, Hubungan Masyarakat, dan Media', 'biro_phm', 'administrasi'),
(6, 'Biro Persidangan I', 'rosid_1', 'persidangan'),
(7, 'Biro Persidangan II', 'rosid_2', 'persidangan'),
(8, 'Biro Sekretariat Pimpinan', 'biro_setpim', 'persidangan'),
(9, 'Pusat Perancangan dan Kajian Kebijakan Hukum', 'pusperjakum', 'persidangan'),
(10, 'Pusat Kajian Daerah dan Anggaran', 'puskadaran', 'persidangan'),
(11, 'Inspektorat Sekretariat Jenderal', 'inspektorat', 'administrasi'),
(12, 'Dukungan Manajemen Dewan', 'dukman_dewan', 'persidangan'),
(13, 'Kantor Daerah', 'kantor_daerah', 'administrasi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_rab_kegiatan`
--

CREATE TABLE `par_rab_kegiatan` (
  `kegiatan_id` int(11) NOT NULL,
  `program_id` varchar(10) NOT NULL,
  `satker_id` int(11) NOT NULL,
  `nama_kegiatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_rab_kegiatan`
--

INSERT INTO `par_rab_kegiatan` (`kegiatan_id`, `program_id`, `satker_id`, `nama_kegiatan`) VALUES
(3856, 'WA', 465224, 'Penatausahaan, Organisasi dan Tata Laksana, Keanggotaan, Pengembangan SDM, dan Advokasi Hukum'),
(3857, 'WA', 465224, 'Perencanaan Dan Pengelolaan Keuangan Sekretariat Jenderal DPD RI'),
(3859, 'WA', 465224, 'Pengelolaan Sistem Teknologi Informasi, Risalah Dan Dokumentasi'),
(3865, 'WA', 465224, 'Penyelenggaraan Keprotokolan, Hubungan Masyarakat Dan Media'),
(3961, 'WA', 465224, 'Penyelenggaraan Pelayanan Umum Sarana Dan Prasarana DPD RI'),
(5240, 'WA', 465224, 'Penyelenggaraan Pengawasan Internal'),
(5241, 'WA', 465224, 'Penyelenggaraan Dukungan Teknis Administratif, Dan Keahlian Di Kantor DPD RI Di Daerah Pemilihan (Ibukota Provinsi)'),
(6383, 'WA', 452646, 'Pengelolaan Keuangan Keanggotaan Dpd Dan Operasional Persidangan Dan Rapat-Rapat'),
(7983, 'CF', 452646, 'Penyelenggaraan Fungsi Legislasi DPD RI'),
(7984, 'CF', 452646, 'Penyelenggaraan Fungsi Legislasi DPD RI'),
(7985, 'CF', 452646, 'Pengelolaan Kerumahtanggan Dan Kerjasama Parlemen DPD RI'),
(7986, 'CF', 452646, 'Pemasyarakatan Produk Hukum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_rab_kro`
--

CREATE TABLE `par_rab_kro` (
  `kro_id` varchar(10) NOT NULL,
  `nama_kro` text NOT NULL,
  `ro_pn` varchar(10) DEFAULT NULL,
  `satuan_1` varchar(50) DEFAULT NULL,
  `satuan_2` varchar(50) DEFAULT NULL,
  `satuan_3` varchar(50) DEFAULT NULL,
  `satuan_4` varchar(50) DEFAULT NULL,
  `satuan_5` varchar(50) DEFAULT NULL,
  `satuan_6` varchar(50) DEFAULT NULL,
  `satuan_7` varchar(50) DEFAULT NULL,
  `satuan_8` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_rab_kro`
--

INSERT INTO `par_rab_kro` (`kro_id`, `nama_kro`, `ro_pn`, `satuan_1`, `satuan_2`, `satuan_3`, `satuan_4`, `satuan_5`, `satuan_6`, `satuan_7`, `satuan_8`) VALUES
('AAA', 'Undang-Undang', 'N', 'UU', 'RUU', NULL, NULL, NULL, NULL, NULL, NULL),
('AAB', 'Peraturan Pemerintah Pengganti Undang-Undang', 'N', 'Perppu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AAC', 'Peraturan Pemerintah', 'N', 'PP', 'RPP', NULL, NULL, NULL, NULL, NULL, NULL),
('AAD', 'Peraturan Presiden', 'N', 'PerPres', 'R.Perpres', NULL, NULL, NULL, NULL, NULL, NULL),
('AAE', 'Keputusan Presiden', 'N', 'KepPres', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AAF', 'Instruksi Presiden', 'N', 'Inpres', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AAG', 'Peraturan Menteri', 'N', 'PerMen', 'Perka', 'Rpermen', NULL, NULL, NULL, NULL, NULL),
('AAH', 'Peraturan lainnya', 'N', 'peraturan', 'RancanganPeraturan', 'SuratKeputusan', NULL, NULL, NULL, NULL, NULL),
('ABA', 'Kebijakan Bidang Ekonomi dan Keuangan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABB', 'Kebijakan Bidang Investasi dan Perdagangan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABC', 'Kebijakan Bidang Politik', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABD', 'Kebijakan Bidang Hukum dan HAM', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABE', 'Kebijakan Bidang Pertahanan dan Keamanan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABF', 'Kebijakan Bidang Sarana dan Prasarana', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABG', 'Kebijakan Bidang Kesehatan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABH', 'Kebijakan Bidang IPTEK, Pendidikan dan Kebudayaan', 'N', 'RekomendasiKebijakan', 'Kajian', 'Rekomendasi', NULL, NULL, NULL, NULL, NULL),
('ABI', 'Kebijakan Bidang Energi dan Sumber Daya Alam', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABJ', 'Kebijakan Bidang Lingkungan Hidup', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABK', 'Kebijakan Bidang Tenaga Kerja, Industri dan UMKM', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABL', 'Kebijakan Bidang Tata Kelola Pemerintahan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABM', 'Kebijakan Bidang Pelayanan Publik', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABN', 'Kebijakan Bidang Sosial', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABO', 'Kebijakan Bidang Teknologi Informasi', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABP', 'Kebijakan Bidang Pengembangan Wilayah', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABQ', 'Kebijakan Bidang Aparatur', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABR', 'Kebijakan Bidang Pertanian dan Perikanan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABS', 'Kebijakan Bidang Ketahanan bencana dan perubahan iklim', 'N', 'RekomendasiKebijakan', 'Kajian', 'Rekomendasi', NULL, NULL, NULL, NULL, NULL),
('ABT', 'Kebijakan Bidang Ruang dan Pertanahan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABU', 'Kebijakan Bidang Tenaga Nuklir', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABV', 'Kebijakan Bidang Kehutanan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ABW', 'Kebijakan Bidang Kemaritiman dan Kelautan', 'N', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('ACA', 'Perizinan Produk', 'N', 'Produk', 'Keputusan', NULL, NULL, NULL, NULL, NULL, NULL),
('ACB', 'Perizinan Masyarakat', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ACC', 'Perizinan Kelompok Masyarakat', 'N', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ACD', 'Perizinan Lembaga', 'N', 'Institusi', 'BadanUsaha', 'Registrasi', NULL, NULL, NULL, NULL, NULL),
('ACE', 'Perizinan Profesi', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ADA', 'Standarisasi Produk', 'N', 'Produk', 'Ekor', 'Peralatan', NULL, NULL, NULL, NULL, NULL),
('ADB', 'Akreditasi Produk', 'N', 'Produk', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ADC', 'Sertifikasi Produk', 'N', 'Produk', 'Sertifikat', NULL, NULL, NULL, NULL, NULL, NULL),
('ADD', 'Standarisasi Lembaga', 'N', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('ADE', 'Akreditasi Lembaga', 'N', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('ADF', 'Sertifikasi Lembaga', 'N', 'Lembaga', 'BadanUsaha', NULL, NULL, NULL, NULL, NULL, NULL),
('ADG', 'Standarisasi Profesi dan SDM', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ADH', 'Akreditasi Profesi dan SDM', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ADI', 'Sertifikasi Profesi dan SDM', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AEB', 'Forum', 'N', 'forum', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AEC', 'Kerja sama', 'N', 'Kesepakatan', 'Dokumen', 'Kegiatan', NULL, NULL, NULL, NULL, NULL),
('AED', 'Perjanjian', 'N', 'perjanjian', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AEF', 'Sosialisasi dan Diseminasi', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AEG', 'Konferensi dan Event', 'N', 'kegiatan', 'PaketKegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('AFA', 'Norma, Standard, Prosedur dan Kriteria', 'N', 'NSPK', 'RancanganStandar', 'Pedoman', 'Standar', NULL, NULL, NULL, NULL),
('AΕA', 'Koordinasi', 'N', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AΕΕ', 'Kemitraan', 'N', 'Kesepakatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('AΕΗ', 'Promosi', 'N', 'promosi', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('BAA', 'Pelayanan Publik kepada masyarakat', 'N', 'Orang', 'Keping', 'Akta', NULL, NULL, NULL, NULL, NULL),
('BAB', 'Pelayanan Publik kepada lembaga', 'N', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('BAC', 'Pelayanan Publik kepada badan usaha', 'N', 'Badanusaha', 'Penyalur', NULL, NULL, NULL, NULL, NULL, NULL),
('BAD', 'Pelayanan Publik kepada industri', 'N', 'Industri', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BAE', 'Pelayanan Publik kepada UMKM', 'N', 'UMKM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BAF', 'Pelayanan Publik kepada Koperasi', 'N', 'Koperasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BAG', 'Pelayanan Publik kepada LSM', 'N', 'LSM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BAH', 'Pelayanan Publik Lainnya', 'N', 'layanan', 'bidang', 'dokumen', 'Bulan', 'MiliarRp', NULL, NULL, NULL),
('BBA', 'Layanan Bantuan Hukum Perseorangan', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BBB', 'Layanan Bantuan Hukum Lembaga', 'N', 'Institusi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BBC', 'Layanan Bantuan Hukum Kelompok Masyarakat', 'N', 'KelompokMasyarakat', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('BBD', 'Layanan Bantuan Hukum Badan Usaha', 'N', 'Badanusaha', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BCA', 'Perkara Hukum Perseorangan', 'N', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('BCB', 'Perkara Hukum Lembaga', 'N', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('BCC', 'Perkara Hukum Kelompok Masyarakat', 'N', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('BCD', 'Perkara Hukum Badan Usaha', 'N', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('BCE', 'Penanganan Perkara', 'N', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('BDA', 'Fasilitasi dan Pembinaan BUMN', 'N', 'BUMN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BDB', 'Fasilitasi dan Pembinaan Lembaga', 'N', 'Lembaga', 'UnitKerja', 'Tim', NULL, NULL, NULL, NULL, NULL),
('BDC', 'Fasilitasi dan Pembinaan Masyarakat', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BDD', 'Fasilitasi dan Pembinaan Kelompok Masyarakat', 'N', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BDE', 'Fasilitasi dan Pembinaan Keluarga', 'N', 'Keluarga', 'KK', NULL, NULL, NULL, NULL, NULL, NULL),
('BDF', 'Fasilitasi dan Pembinaan Koperasi', 'N', 'Koperasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BDG', 'Fasilitasi dan Pembinaan UMKM', 'N', 'UMKM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BDH', 'Fasilitasi dan Pembinaan Badan Usaha', 'N', 'Badanusaha', 'MiliarUSD', NULL, NULL, NULL, NULL, NULL, NULL),
('BDI', 'Fasilitasi dan Pembinaan Industri', 'N', 'Industri', 'IKM', 'MiliarUSD', NULL, NULL, NULL, NULL, NULL),
('BDJ', 'Fasilitasi dan Pembinaan Start up', 'N', 'Startup', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEA', 'Bantuan Masyarakat', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEB', 'Bantuan Keluarga', 'N', 'Keluarga', 'KK', NULL, NULL, NULL, NULL, NULL, NULL),
('BEC', 'Bantuan Produk', 'N', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BED', 'Bantuan Tanaman', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEE', 'Bantuan Kebencanaan', 'N', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEF', 'Bantuan Luar Negeri', 'N', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEG', 'Bantuan Peralatan / Sarana', 'N', 'Unit', 'Paket', 'SR', 'Titik', NULL, NULL, NULL, NULL),
('BEH', 'Bantuan Kelompok Masyarakat', 'N', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEI', 'Bantuan Lembaga', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEJ', 'Bantuan Pendidikan Tinggi', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEK', 'Bantuan Pendidikan Dasar dan Menengah', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEL', 'Bantuan Hewan', 'N', 'Ekor', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BEM', 'Bantuan Pelaku Usaha', 'N', 'PelakuUsaha', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BFA', 'Subsidi kepada Masyarakat', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BFB', 'Subsidi kepada Lembaga', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BFC', 'Subsidi Kepada Keluarga', 'N', 'RumahTangga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGA', 'Tata Kelola Kelembagaan Publik Bidang Ekonomi', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGB', 'Tata Kelola Kelembagaan Publik Bidang Sosial dan Budaya', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGC', 'Tata Kelola Kelembagaan Publik Bidang Pendidikan', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGD', 'Tata Kelola Kelembagaan Publik Bidang Kesehatan', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGE', 'Tata Kelola Kelembagaan Publik Bidang Politik dan Hukum', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BGF', 'Tata Kelola Kelembagaan Publik Bidang Pertahanan dan Keamanan', 'N', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BHA', 'Operasi Bidang Pertahanan', 'N', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BHB', 'Operasi Bidang Keamanan', 'N', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BHC', 'Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BHD', 'Operasi Pengawasan Sumber Daya Alam', 'N', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BIA', 'Pengawasan dan Pengendalian Produk', 'N', 'Produk', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BIB', 'Pengawasan dan Pengendalian Masyarakat', 'N', 'Orang', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BIC', 'Pengawasan dan Pengendalian Lembaga', 'N', 'Lembaga', 'Laporan', 'BadanUsaha', 'Penyalur', NULL, NULL, NULL, NULL),
('BID', 'Pengawasan dan Pengendalian Kelompok Masyarakat', 'N', 'KelompokMasyarakat', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BIE', 'Pengawasan dan Pengendalian Pemerintah Daerah', 'N', 'PemerintahDaerah', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BIF', 'Pengawasan dan Pengendalian Layanan', 'N', 'Layanan', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BIG', 'Pemeriksaan dan Audit Penerimaan', 'N', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BIH', 'Pengawasan dan Pengendalian Badan Usaha', 'N', 'BadanUsaha', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BII', 'Pengawasan dan Pengendalian Lingkungan', 'N', 'Hektar', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('BJA', 'Penyidikan dan Pengujian Produk', 'N', 'Produk', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BJB', 'Penyidikan dan Pengujian Peralatan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BJC', 'Penyidikan dan Pengujian Penyakit', 'N', 'Sampel', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BKA', 'Pemantauan masyarakat dan kelompok masyarakat', 'N', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BKB', 'Pemantauan produk', 'N', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BKC', 'Pemantauan lembaga', 'N', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BLA', 'Persidangan Lembaga Legislatif', 'N', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BLB', 'Persidangan Lembaga Eksekutif', 'N', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BLC', 'Persidangan Lembaga Yudikatif', 'N', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('BMA', 'Data dan Informasi Publik', 'N', 'layanan', 'dokumen', 'publikasi', 'Wilayah', 'Peta', 'Data', NULL, NULL),
('BMB', 'Komunikasi Publik', 'N', 'layanan', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('CAA', 'Sarana Bidang Pendidikan', 'N', 'Paket', 'Unit', 'm2', NULL, NULL, NULL, NULL, NULL),
('CAB', 'Sarana Bidang Kesehatan', 'N', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('CAC', 'Sarana Bidang Konektivitas Darat', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CAD', 'Sarana Bidang Konektivitas Udara', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CAE', 'Sarana Bidang Konektivitas Laut', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAF', 'Sarana Bidang Pertahanan dan Keamanan', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CAG', 'Sarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAH', 'Sarana Bidang Industri dan Perdagangan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAI', 'Sarana Pengembangan Kawasan', 'N', 'Unit', 'Hektar', NULL, NULL, NULL, NULL, NULL, NULL),
('CAJ', 'Sarana Bidang Ketenagakerjaan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAK', 'Sarana Bidang Konektivitas Perkeretaapian', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CAL', 'Sarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAM', 'Sarana Bidang Pariwisata, Ekonomi Kreatif dan Kebudayaan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAN', 'Sarana Bidang Teknologi Informasi dan Komunikasi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAO', 'Sarana Bidang IPTEK', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CAP', 'Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBA', 'Prasarana Bidang Konektivitas Perkeretaapian', 'N', 'km', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL),
('CBB', 'Prasarana Bidang Perumahan dan Pemukiman', 'N', 'Unit', 'Hektar', 'KK', 'Liter/detik', 'SR', NULL, NULL, NULL),
('CBC', 'Prasarana Bidang Konektivitas Darat (Jalan)', 'N', 'km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBD', 'Prasarana Bidang Konektivitas Laut', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBE', 'Prasarana Bidang Konektivitas Udara', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CBF', 'Prasarana Bidang Konektivitas Darat (Jembatan)', 'N', 'm', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBG', 'Prasarana Bidang SDA dan Irigasi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBH', 'Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBI', 'Prasarana Bidang Pendidikan Dasar dan Menengah', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBJ', 'Prasarana Bidang Pendidikan Tinggi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBK', 'Prasarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBL', 'Prasarana Bidang Industri dan Perdagangan', 'N', 'Unit', 'Ruas', NULL, NULL, NULL, NULL, NULL, NULL),
('CBM', 'Prasarana Bidang Pertahanan dan Keamanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBN', 'Prasarana Bidang Pariwisata dan Kebudayaan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBO', 'Prasarana Pengembangan Kawasan', 'N', 'km2', 'bidang', NULL, NULL, NULL, NULL, NULL, NULL),
('CBP', 'Prasarana Bidang Konektivitas Darat', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CBQ', 'Prasarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBR', 'Dukungan Teknis', 'N', 'Dokumen', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBS', 'Prasarana Jaringan Sumber Daya Air', 'N', 'Km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBT', 'Prasarana Bidang Teknologi Informasi dan Komunikasi', 'N', 'Unit', 'Kab/Kota', 'Kecamatan', 'Titik/Lokasi', NULL, NULL, NULL, NULL),
('CBU', 'Prasarana Bidang IPTEK', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CBV', 'Prasarana Bidang Kesehatan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCA', 'OM Sarana Bidang Pendidikan', 'N', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('CCB', 'OM Sarana Bidang Kesehatan', 'N', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('CCC', 'OM Sarana Bidang Konektivitas Darat', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CCD', 'OM Sarana Bidang Konektivitas Udara', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CCE', 'OM Sarana Bidang Konektivitas Laut', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCF', 'OM Sarana Bidang Pertahanan dan Keamanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCG', 'OM Sarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCH', 'OM Sarana Bidang Industri dan Perdagangan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCI', 'OM Sarana Pengembangan Kawasan', 'N', 'Unit', 'Hektar', NULL, NULL, NULL, NULL, NULL, NULL),
('CCJ', 'OM Sarana Bidang Ketenagakerjaan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCK', 'OM Sarana Bidang Konektivitas Perkeretaapian', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CCL', 'OM Sarana Bidang Teknologi Informasi dan Komunikasi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CCM', 'OM Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDA', 'OM Prasarana Bidang Konektivitas Perkeretaapian', 'N', 'km', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL),
('CDB', 'OM Prasarana Bidang Perumahan dan Pemukiman', 'N', 'Unit', 'Hektar', 'KK', 'Liter/detik', 'SR', NULL, NULL, NULL),
('CDC', 'OM Prasarana Bidang Konektivitas Darat (Jalan)', 'N', 'km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDD', 'OM Prasarana Bidang Konektivitas Laut', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDE', 'OM Prasarana Bidang Konektivitas Udara', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CDF', 'OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'N', 'm', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDG', 'OM Prasarana Bidang SDA dan Irigasi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDH', 'OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDI', 'OM Prasarana Bidang Pendidikan Dasar dan Menengah', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDJ', 'OM Prasarana Bidang Pendidikan Tinggi', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDK', 'OM Prasarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDL', 'OM Prasarana Bidang Industri dan Perdagangan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDM', 'OM Prasarana Bidang Pertahanan dan Keamanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDN', 'OM Prasarana Bidang Pariwisata dan Kebudayaan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDO', 'OM Prasarana Pengembangan Kawasan', 'N', 'km2', 'bidang', NULL, NULL, NULL, NULL, NULL, NULL),
('CDP', 'OM Prasarana Bidang Konektivitas Darat', 'N', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('CDQ', 'OM Prasarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDR', 'OM Prasarana Jaringan Sumber Daya Air', 'N', 'Km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CDS', 'OM Prasarana Bidang Teknologi Informasi dan Komunikasi', 'N', 'Unit', 'Kab/Kota', 'Kecamatan', 'Titik/Lokasi', NULL, NULL, NULL, NULL),
('CDT', 'OM Prasarana Bidang IPTEK', 'N', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CEA', 'Konservasi Kawasan/Rehabilitasi Ekosistem', 'N', 'Hektar', 'Ton', NULL, NULL, NULL, NULL, NULL, NULL),
('CEB', 'Konservasi Jenis/Spesies', 'N', 'Jenis', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAA', 'Pendidikan Vokasi Bidang Komunikasi dan Informatika', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAB', 'Pendidikan Vokasi Bidang Infrastruktur', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAC', 'Pendidikan Vokasi Bidang Pertanian dan Perikanan', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAD', 'Pendidikan Vokasi Bidang Pariwisata dan Kebudayaan', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAE', 'Pendidikan Vokasi Bidang Kehutananan dan Lingkungan Hidup', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAF', 'Pendidikan Vokasi Bidang Kesehatan', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DAG', 'Pendidikan Vokasi Bidang Industri', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DBA', 'Pendidikan Tinggi', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DBB', 'Pendidikan Menengah', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DBC', 'Pendidikan Dasar', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DBD', 'Pendidikan Pra-Sekolah', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DBE', 'Pendidikan Non Gelar', 'N', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('DCA', 'Pelatihan Bidang Komunikasi dan Informatika', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCB', 'Pelatihan Bidang Infrastruktur', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCC', 'Pelatihan Bidang Pertanian dan Perikanan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCD', 'Pelatihan Bidang Pariwisata dan Kebudayaan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCE', 'Pelatihan Bidang Kehutananan dan Lingkungan Hidup', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCF', 'Pelatihan Bidang Ekonomi dan Keuangan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCG', 'Pelatihan Bidang Pertahanan dan Keamanan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCH', 'Pelatihan Bidang Industri', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCI', 'Pelatihan Bidang Pendidikan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCJ', 'Pelatihan Bidang Sosial', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCK', 'Pelatihan Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCL', 'Pelatihan Bidang Ekonomi Kreatif', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCM', 'Pelatihan Bidang Kesehatan', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DCN', 'Pelatihan Bidang IPTEK', 'N', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('DDA', 'Penelitian dan Pengembangan Produk', 'N', 'Produk', 'Bibit/Benih', 'Ekor', NULL, NULL, NULL, NULL, NULL),
('DDB', 'Penelitian dan Pengembangan Purwarupa', 'N', 'Purwarupa', 'Desain', NULL, NULL, NULL, NULL, NULL, NULL),
('DDC', 'Penelitian dan Pengembangan Modeling', 'N', 'model', 'Desain', NULL, NULL, NULL, NULL, NULL, NULL),
('DDD', 'Penelitian dan Pengembangan yang Dipatenkan', 'N', 'kekayaanintelektual', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('EBA', 'Layanan Dukungan Manajemen Internal', 'N', 'Layanan', 'Laporan', 'Dokumen', NULL, NULL, NULL, NULL, NULL),
('EBB', 'Layanan Sarana dan Prasarana Internal', 'N', 'Unit', 'm2.Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('EBC', 'Layanan Manajemen SDM Internal', 'N', 'Orang', 'Layanan', NULL, NULL, NULL, NULL, NULL, NULL),
('EBD', 'Layanan Manajemen Kinerja Internal', 'N', 'Dokumen', 'Layanan', 'Laporan', NULL, NULL, NULL, NULL, NULL),
('FAA', 'Kearsipan', 'N', 'Dokumen', 'Arsip', NULL, NULL, NULL, NULL, NULL, NULL),
('FAB', 'Sistem Informasi Pemerintahan', 'N', 'SistemInformasi', 'ModulAplikasi', 'Layanan', NULL, NULL, NULL, NULL, NULL),
('FAC', 'Peningkatan Kapasitas Aparatur Negara', 'N', 'Orang', 'K/L', 'Daerah', 'UnitKerja', NULL, NULL, NULL, NULL),
('FAD', 'Perencanaan dan Penganggaran', 'N', 'layanan', 'dokumen', NULL, NULL, NULL, NULL, NULL, NULL),
('FAE', 'Pemantauan dan Evaluasi serta Pelaporan', 'N', 'laporan', 'rekomendasi', NULL, NULL, NULL, NULL, NULL, NULL),
('FAF', 'Pemeriksaan Keuangan Negara', 'N', 'laporan', 'LHP', 'PendapatHukum', 'BahanPertimbangan', 'Pertimbangan', NULL, NULL, NULL),
('FAG', 'Pengawasan Pembangunan', 'N', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('FAH', 'Pengelolaan Keuangan Negara', 'N', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('FAI', 'Peningkatan Manajemen Lembaga Pemerintahan', 'N', 'Lembaga', 'K/L', 'Pemda', 'UnitKerja', NULL, NULL, NULL, NULL),
('FAJ', 'Benda Materai dan Cukai', 'N', 'Keping', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('FAK', 'Pengelolaan Aset BUN', 'N', 'Unit', 'Aset', NULL, NULL, NULL, NULL, NULL, NULL),
('FAL', 'Pengelolaan Pelaksanaan Anggaran dan Pembiayaan', 'N', 'Dokumen', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('FAM', 'Hasil Kelolaan Dana', 'N', 'Rupiah', 'Hektar', 'Orang', 'UsahaMikro', 'milyar', NULL, NULL, NULL),
('FBA', 'Fasilitasi dan Pembinaan Pemerintah Daerah', 'N', 'Daerah(Prov/Kab/Kota)', 'Provinsi', 'Kab/Kota', NULL, NULL, NULL, NULL, NULL),
('FBB', 'Fasilitasi dan Pembinaan Pemerintah Desa', 'N', 'Desa', 'Desa/Kelurahan', NULL, NULL, NULL, NULL, NULL, NULL),
('ODC', 'Fasilitasi dan Pembinaan Masyarakat', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PAA', 'Undang-Undang', 'Y', 'UU', 'RUU', NULL, NULL, NULL, NULL, NULL, NULL),
('PAB', 'Peraturan Pemerintah Pengganti Undang-Undang', 'Y', 'Perppu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PAC', 'Peraturan Pemerintah', 'Y', 'PP', 'RPP', NULL, NULL, NULL, NULL, NULL, NULL),
('PAD', 'Peraturan Presiden', 'Y', 'PerPres', 'R.Perpres', NULL, NULL, NULL, NULL, NULL, NULL),
('PAE', 'Keputusan Presiden', 'Y', 'KepPres', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PAF', 'Instruksi Presiden', 'Y', 'Inpres', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PAG', 'Peraturan Menteri', 'Y', 'PerMen', 'Perka', 'Rpermen', NULL, NULL, NULL, NULL, NULL),
('PAH', 'Peraturan lainnya', 'Y', 'peraturan', 'RancanganPeraturan', 'SuratKeputusan', NULL, NULL, NULL, NULL, NULL),
('PBA', 'Kebijakan Bidang Ekonomi dan Keuangan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBB', 'Kebijakan Bidang Investasi dan Perdagangan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBC', 'Kebijakan Bidang Politik', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBD', 'Kebijakan Bidang Hukum dan HAM', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBE', 'Kebijakan Bidang Pertahanan dan Keamanan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBF', 'Kebijakan Bidang Sarana dan Prasarana', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBG', 'Kebijakan Bidang Kesehatan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBH', 'Kebijakan Bidang IPTEK, Pendidikan dan Kebudayaan', 'Y', 'RekomendasiKebijakan', 'Kajian', 'Rekomendasi', NULL, NULL, NULL, NULL, NULL),
('PBI', 'Kebijakan Bidang Energi dan Sumber Daya Alam', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBJ', 'Kebijakan Bidang Lingkungan Hidup', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBK', 'Kebijakan Bidang Tenaga Kerja, Industri dan UMKM', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBL', 'Kebijakan Bidang Tata Kelola Pemerintahan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBM', 'Kebijakan Bidang Pelayanan Publik', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBN', 'Kebijakan Bidang Sosial', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBO', 'Kebijakan Bidang Teknologi Informasi', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBP', 'Kebijakan Bidang Pengembangan Wilayah', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBQ', 'Kebijakan Bidang Aparatur', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBR', 'Kebijakan Bidang Pertanian dan Perikanan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBS', 'Kebijakan Bidang Ketahanan bencana dan perubahan iklim', 'Y', 'RekomendasiKebijakan', 'Kajian', 'Rekomendasi', NULL, NULL, NULL, NULL, NULL),
('PBT', 'Kebijakan Bidang Ruang dan Pertanahan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBU', 'Kebijakan Bidang Tenaga Nuklir', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBV', 'Kebijakan Bidang Kehutanan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PBW', 'Kebijakan Bidang Kemaritiman dan Kelautan', 'Y', 'RekomendasiKebijakan', 'Kajian', NULL, NULL, NULL, NULL, NULL, NULL),
('PCA', 'Perizinan Produk', 'Y', 'Produk', 'Keputusan', NULL, NULL, NULL, NULL, NULL, NULL),
('PCB', 'Perizinan Masyarakat', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PCC', 'Perizinan Kelompok Masyarakat', 'Y', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PCD', 'Perizinan Lembaga', 'Y', 'Institusi', 'BadanUsaha', 'Registrasi', NULL, NULL, NULL, NULL, NULL),
('PCE', 'Perizinan Profesi', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PDA', 'Standarisasi Produk', 'Y', 'Produk', 'Ekor', 'Peralatan', NULL, NULL, NULL, NULL, NULL),
('PDB', 'Akreditasi Produk', 'Y', 'Produk', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PDC', 'Sertifikasi Produk', 'Y', 'Produk', 'Sertifikat', NULL, NULL, NULL, NULL, NULL, NULL),
('PDD', 'Standarisasi Lembaga', 'Y', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('PDE', 'Akreditasi Lembaga', 'Y', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('PDF', 'Sertifikasi Lembaga', 'Y', 'Lembaga', 'BadanUsaha', NULL, NULL, NULL, NULL, NULL, NULL),
('PDG', 'Standarisasi Profesi dan SDM', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PDH', 'Akreditasi Profesi dan SDM', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PDI', 'Sertifikasi Profesi dan SDM', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEA', 'Koordinasi', 'Y', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEB', 'Forum', 'Y', 'forum', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEC', 'Kerja sama', 'Y', 'Kesepakatan', 'Dokumen', 'Kegiatan', NULL, NULL, NULL, NULL, NULL),
('PED', 'Perjanjian', 'Y', 'perjanjian', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEE', 'Kemitraan', 'Y', 'Kesepakatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEF', 'Sosialisasi dan Diseminasi', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PEG', 'Konferensi dan Event', 'Y', 'kegiatan', 'PaketKegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('PEH', 'Promosi', 'Y', 'promosi', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('PFA', 'Norma, Standard, Prosedur dan Kriteria', 'Y', 'NSPK', 'RancanganStandar', 'Pedoman', 'Standar', NULL, NULL, NULL, NULL),
('QAA', 'Pelayanan Publik kepada masyarakat', 'Y', 'Orang', 'Keping', 'Akta', NULL, NULL, NULL, NULL, NULL),
('QAB', 'Pelayanan Publik kepada lembaga', 'Y', 'Lembaga', 'UnitKerja', NULL, NULL, NULL, NULL, NULL, NULL),
('QAC', 'Pelayanan Publik kepada badan usaha', 'Y', 'Badanusaha', 'Penyalur', NULL, NULL, NULL, NULL, NULL, NULL),
('QAD', 'Pelayanan Publik kepada industri', 'Y', 'Industri', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QAE', 'Pelayanan Publik kepada UMKM', 'Y', 'UMKM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QAF', 'Pelayanan Publik kepada Koperasi', 'Y', 'Koperasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QAG', 'Pelayanan Publik kepada LSM', 'Y', 'LSM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QAH', 'Pelayanan Publik Lainnya', 'Y', 'layanan', 'bidang', 'dokumen', 'Bulan', 'MiliarRp', NULL, NULL, NULL),
('QBA', 'Layanan Bantuan Hukum Perseorangan', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QBB', 'Layanan Bantuan Hukum Lembaga', 'Y', 'Institusi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QBC', 'Layanan Bantuan Hukum Kelompok Masyarakat', 'Y', 'KelompokMasyarakat', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('QBD', 'Layanan Bantuan Hukum Badan Usaha', 'Y', 'Badanusaha', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QCA', 'Perkara Hukum Perseorangan', 'Y', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('QCB', 'Perkara Hukum Lembaga', 'Y', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('QCC', 'Perkara Hukum Kelompok Masyarakat', 'Y', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('QCD', 'Perkara Hukum Badan Usaha', 'Y', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('QCE', 'Penanganan Perkara', 'Y', 'Perkara', 'BerkasPerkara', NULL, NULL, NULL, NULL, NULL, NULL),
('QDA', 'Fasilitasi dan Pembinaan BUMN', 'Y', 'BUMN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QDB', 'Fasilitasi dan Pembinaan Lembaga', 'Y', 'Lembaga', 'UnitKerja', 'Tim', NULL, NULL, NULL, NULL, NULL),
('QDD', 'Fasilitasi dan Pembinaan Kelompok Masyarakat', 'Y', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QDE', 'Fasilitasi dan Pembinaan Keluarga', 'Y', 'Keluarga', 'KK', NULL, NULL, NULL, NULL, NULL, NULL),
('QDF', 'Fasilitasi dan Pembinaan Koperasi', 'Y', 'Koperasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QDG', 'Fasilitasi dan Pembinaan UMKM', 'Y', 'UMKM', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QDH', 'Fasilitasi dan Pembinaan Badan Usaha', 'Y', 'Badanusaha', 'MiliarUSD', NULL, NULL, NULL, NULL, NULL, NULL),
('QDI', 'Fasilitasi dan Pembinaan Industri', 'Y', 'Industri', 'IKM', 'MiliarUSD', NULL, NULL, NULL, NULL, NULL),
('QDJ', 'Fasilitasi dan Pembinaan Start up', 'Y', 'Startup', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEA', 'Bantuan Masyarakat', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEB', 'Bantuan Keluarga', 'Y', 'Keluarga', 'KK', NULL, NULL, NULL, NULL, NULL, NULL),
('QEC', 'Bantuan Produk', 'Y', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QED', 'Bantuan Tanaman', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEE', 'Bantuan Kebencanaan', 'Y', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEF', 'Bantuan Luar Negeri', 'Y', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEG', 'Bantuan Peralatan / Sarana', 'Y', 'Unit', 'Paket', 'SR', 'Titik', NULL, NULL, NULL, NULL),
('QEH', 'Bantuan Kelompok Masyarakat', 'Y', 'KelompokMasyarakat', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEI', 'Bantuan Lembaga', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEJ', 'Bantuan Pendidikan Tinggi', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEK', 'Bantuan Pendidikan Dasar dan Menengah', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEL', 'Bantuan Hewan', 'Y', 'Ekor', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QEM', 'Bantuan Pelaku Usaha', 'Y', 'PelakuUsaha', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QFA', 'Subsidi kepada Masyarakat', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QFB', 'Subsidi kepada Lembaga', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QFC', 'Subsidi Kepada Keluarga', 'Y', 'RumahTangga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGA', 'Tata Kelola Kelembagaan Publik Bidang Ekonomi', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGB', 'Tata Kelola Kelembagaan Publik Bidang Sosial dan Budaya', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGC', 'Tata Kelola Kelembagaan Publik Bidang Pendidikan', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGD', 'Tata Kelola Kelembagaan Publik Bidang Kesehatan', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGE', 'Tata Kelola Kelembagaan Publik Bidang Politik dan Hukum', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QGF', 'Tata Kelola Kelembagaan Publik Bidang Pertahanan dan Keamanan', 'Y', 'Lembaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QHA', 'Operasi Bidang Pertahanan', 'Y', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QHB', 'Operasi Bidang Keamanan', 'Y', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QHC', 'Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QHD', 'Operasi Pengawasan Sumber Daya Alam', 'Y', 'operasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QIA', 'Pengawasan dan Pengendalian Produk', 'Y', 'Produk', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QIB', 'Pengawasan dan Pengendalian Masyarakat', 'Y', 'Orang', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QIC', 'Pengawasan dan Pengendalian Lembaga', 'Y', 'Lembaga', 'Laporan', 'BadanUsaha', 'Penyalur', NULL, NULL, NULL, NULL),
('QID', 'Pengawasan dan Pengendalian Kelompok Masyarakat', 'Y', 'KelompokMasyarakat', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QIE', 'Pengawasan dan Pengendalian Pemerintah Daerah', 'Y', 'PemerintahDaerah', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QIF', 'Pengawasan dan Pengendalian Layanan', 'Y', 'Layanan', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QIG', 'Pemeriksaan dan Audit Penerimaan', 'Y', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QIH', 'Pengawasan dan Pengendalian Badan Usaha', 'Y', 'BadanUsaha', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QII', 'Pengawasan dan Pengendalian Lingkungan', 'Y', 'Hektar', 'Laporan', NULL, NULL, NULL, NULL, NULL, NULL),
('QJA', 'Penyidikan dan Pengujian Produk', 'Y', 'Produk', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QJB', 'Penyidikan dan Pengujian Peralatan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QJC', 'Penyidikan dan Pengujian Penyakit', 'Y', 'Sampel', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QKA', 'Pemantauan masyarakat dan kelompok masyarakat', 'Y', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QKB', 'Pemantauan produk', 'Y', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QKC', 'Pemantauan lembaga', 'Y', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QLA', 'Persidangan Lembaga Legislatif', 'Y', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QLB', 'Persidangan Lembaga Eksekutif', 'Y', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QLC', 'Persidangan Lembaga Yudikatif', 'Y', 'sidang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('QMA', 'Data dan Informasi Publik', 'Y', 'layanan', 'dokumen', 'publikasi', 'Wilayah', 'Peta', 'Data', NULL, NULL),
('QMB', 'Komunikasi Publik', 'Y', 'layanan', 'kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('RAA', 'Sarana Bidang Pendidikan', 'Y', 'Paket', 'Unit', 'm2', NULL, NULL, NULL, NULL, NULL),
('RAB', 'Sarana Bidang Kesehatan', 'Y', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('RAC', 'Sarana Bidang Konektivitas Darat', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RAD', 'Sarana Bidang Konektivitas Udara', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RAE', 'Sarana Bidang Konektivitas Laut', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAF', 'Sarana Bidang Pertahanan dan Keamanan', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RAG', 'Sarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAH', 'Sarana Bidang Industri dan Perdagangan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAI', 'Sarana Pengembangan Kawasan', 'Y', 'Unit', 'Hektar', NULL, NULL, NULL, NULL, NULL, NULL),
('RAJ', 'Sarana Bidang Ketenagakerjaan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAK', 'Sarana Bidang Konektivitas Perkeretaapian', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RAL', 'Sarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAM', 'Sarana Bidang Pariwisata, Ekonomi Kreatif dan Kebudayaan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAN', 'Sarana Bidang Teknologi Informasi dan Komunikasi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAO', 'Sarana Bidang IPTEK', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RAP', 'Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBA', 'Prasarana Bidang Konektivitas Perkeretaapian', 'Y', 'km', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL),
('RBB', 'Prasarana Bidang Perumahan dan Pemukiman', 'Y', 'Unit', 'Hektar', 'KK', 'Liter/detik', 'SR', NULL, NULL, NULL),
('RBC', 'Prasarana Bidang Konektivitas Darat (Jalan)', 'Y', 'km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBD', 'Prasarana Bidang Konektivitas Laut', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBE', 'Prasarana Bidang Konektivitas Udara', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RBF', 'Prasarana Bidang Konektivitas Darat (Jembatan)', 'Y', 'm', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBG', 'Prasarana Bidang SDA dan Irigasi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBH', 'Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBI', 'Prasarana Bidang Pendidikan Dasar dan Menengah', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBJ', 'Prasarana Bidang Pendidikan Tinggi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBK', 'Prasarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBL', 'Prasarana Bidang Industri dan Perdagangan', 'Y', 'Unit', 'Ruas', NULL, NULL, NULL, NULL, NULL, NULL),
('RBM', 'Prasarana Bidang Pertahanan dan Keamanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBN', 'Prasarana Bidang Pariwisata dan Kebudayaan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBO', 'Prasarana Pengembangan Kawasan', 'Y', 'km2', 'bidang', NULL, NULL, NULL, NULL, NULL, NULL),
('RBP', 'Prasarana Bidang Konektivitas Darat', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RBQ', 'Prasarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBR', 'Dukungan Teknis', 'Y', 'Dokumen', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBS', 'Prasarana Jaringan Sumber Daya Air', 'Y', 'Km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBT', 'Prasarana Bidang Teknologi Informasi dan Komunikasi', 'Y', 'Unit', 'Kab/Kota', 'Kecamatan', 'Titik/Lokasi', NULL, NULL, NULL, NULL),
('RBU', 'Prasarana Bidang IPTEK', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RBV', 'Prasarana Bidang Kesehatan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCA', 'OM Sarana Bidang Pendidikan', 'Y', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('RCB', 'OM Sarana Bidang Kesehatan', 'Y', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL),
('RCC', 'OM Sarana Bidang Konektivitas Darat', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RCD', 'OM Sarana Bidang Konektivitas Udara', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RCE', 'OM Sarana Bidang Konektivitas Laut', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCF', 'OM Sarana Bidang Pertahanan dan Keamanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCG', 'OM Sarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCH', 'OM Sarana Bidang Industri dan Perdagangan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCI', 'OM Sarana Pengembangan Kawasan', 'Y', 'Unit', 'Hektar', NULL, NULL, NULL, NULL, NULL, NULL),
('RCJ', 'OM Sarana Bidang Ketenagakerjaan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCK', 'OM Sarana Bidang Konektivitas Perkeretaapian', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RCL', 'OM Sarana Bidang Teknologi Informasi dan Komunikasi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RCM', 'OM Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDA', 'OM Prasarana Bidang Konektivitas Perkeretaapian', 'Y', 'km', 'Paket', 'Unit', NULL, NULL, NULL, NULL, NULL),
('RDB', 'OM Prasarana Bidang Perumahan dan Pemukiman', 'Y', 'Unit', 'Hektar', 'KK', 'Liter/detik', 'SR', NULL, NULL, NULL),
('RDC', 'OM Prasarana Bidang Konektivitas Darat (Jalan)', 'Y', 'km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDD', 'OM Prasarana Bidang Konektivitas Laut', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDE', 'OM Prasarana Bidang Konektivitas Udara', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RDF', 'OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'Y', 'm', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDG', 'OM Prasarana Bidang SDA dan Irigasi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDH', 'OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDI', 'OM Prasarana Bidang Pendidikan Dasar dan Menengah', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDJ', 'OM Prasarana Bidang Pendidikan Tinggi', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDK', 'OM Prasarana Bidang Pertanian, Kehutanan dan Lingkungan Hidup', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDL', 'OM Prasarana Bidang Industri dan Perdagangan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDM', 'OM Prasarana Bidang Pertahanan dan Keamanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDN', 'OM Prasarana Bidang Pariwisata dan Kebudayaan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDO', 'OM Prasarana Pengembangan Kawasan', 'Y', 'km2', 'bidang', NULL, NULL, NULL, NULL, NULL, NULL),
('RDP', 'OM Prasarana Bidang Konektivitas Darat', 'Y', 'Unit', 'Paket', NULL, NULL, NULL, NULL, NULL, NULL),
('RDQ', 'OM Prasarana Bidang Kemaritiman, Kelautan, dan Perikanan', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDR', 'OM Prasarana Jaringan Sumber Daya Air', 'Y', 'Km', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RDS', 'OM Prasarana Bidang Teknologi Informasi dan Komunikasi', 'Y', 'Unit', 'Kab/Kota', 'Kecamatan', 'Titik/Lokasi', NULL, NULL, NULL, NULL),
('RDT', 'OM Prasarana Bidang IPTEK', 'Y', 'Unit', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('REA', 'Konservasi Kawasan/Rehabilitasi Ekosistem', 'Y', 'Hektar', 'Ton', NULL, NULL, NULL, NULL, NULL, NULL),
('REB', 'Konservasi Jenis/Spesies', 'Y', 'Jenis', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAA', 'Pendidikan Vokasi Bidang Komunikasi dan Informatika', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAB', 'Pendidikan Vokasi Bidang Infrastruktur', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAC', 'Pendidikan Vokasi Bidang Pertanian dan Perikanan', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAD', 'Pendidikan Vokasi Bidang Pariwisata dan Kebudayaan', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAE', 'Pendidikan Vokasi Bidang Kehutananan dan Lingkungan Hidup', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAF', 'Pendidikan Vokasi Bidang Kesehatan', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SAG', 'Pendidikan Vokasi Bidang Industri', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SBA', 'Pendidikan Tinggi', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SBB', 'Pendidikan Menengah', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SBC', 'Pendidikan Dasar', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SBD', 'Pendidikan Pra-Sekolah', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SBE', 'Pendidikan Non Gelar', 'Y', 'Orang', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('SCA', 'Pelatihan Bidang Komunikasi dan Informatika', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCB', 'Pelatihan Bidang Infrastruktur', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCC', 'Pelatihan Bidang Pertanian dan Perikanan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCD', 'Pelatihan Bidang Pariwisata dan Kebudayaan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCE', 'Pelatihan Bidang Kehutananan dan Lingkungan Hidup', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCF', 'Pelatihan Bidang Ekonomi dan Keuangan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCG', 'Pelatihan Bidang Pertahanan dan Keamanan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCH', 'Pelatihan Bidang Industri', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCI', 'Pelatihan Bidang Pendidikan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `par_rab_kro` (`kro_id`, `nama_kro`, `ro_pn`, `satuan_1`, `satuan_2`, `satuan_3`, `satuan_4`, `satuan_5`, `satuan_6`, `satuan_7`, `satuan_8`) VALUES
('SCJ', 'Pelatihan Bidang Sosial', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCK', 'Pelatihan Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCL', 'Pelatihan Bidang Ekonomi Kreatif', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCM', 'Pelatihan Bidang Kesehatan', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SCN', 'Pelatihan Bidang IPTEK', 'Y', 'Orang', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('SDA', 'Penelitian dan Pengembangan Produk', 'Y', 'Produk', 'Bibit/Benih', 'Ekor', NULL, NULL, NULL, NULL, NULL),
('SDB', 'Penelitian dan Pengembangan Purwarupa', 'Y', 'Purwarupa', 'Desain', NULL, NULL, NULL, NULL, NULL, NULL),
('SDC', 'Penelitian dan Pengembangan Modeling', 'Y', 'model', 'Desain', NULL, NULL, NULL, NULL, NULL, NULL),
('SDD', 'Penelitian dan Pengembangan yang Dipatenkan', 'Y', 'kekayaanintelektual', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UAA', 'Kearsipan', 'Y', 'Dokumen', 'Arsip', NULL, NULL, NULL, NULL, NULL, NULL),
('UAB', 'Sistem Informasi Pemerintahan', 'Y', 'SistemInformasi', 'ModulAplikasi', 'Layanan', NULL, NULL, NULL, NULL, NULL),
('UAC', 'Peningkatan Kapasitas Aparatur Negara', 'Y', 'Orang', 'K/L', 'Daerah', 'UnitKerja', NULL, NULL, NULL, NULL),
('UAD', 'Perencanaan dan Penganggaran', 'Y', 'layanan', 'dokumen', NULL, NULL, NULL, NULL, NULL, NULL),
('UAE', 'Pemantauan dan Evaluasi serta Pelaporan', 'Y', 'laporan', 'rekomendasi', NULL, NULL, NULL, NULL, NULL, NULL),
('UAF', 'Pemeriksaan Keuangan Negara', 'Y', 'laporan', 'LHP', 'PendapatHukum', 'BahanPertimbangan', 'Pertimbangan', NULL, NULL, NULL),
('UAG', 'Pengawasan Pembangunan', 'Y', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UAH', 'Pengelolaan Keuangan Negara', 'Y', 'laporan', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UAI', 'Peningkatan Manajemen Lembaga Pemerintahan', 'Y', 'Lembaga', 'K/L', 'Pemda', 'UnitKerja', NULL, NULL, NULL, NULL),
('UAJ', 'Benda Materai dan Cukai', 'Y', 'Keping', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UAK', 'Pengelolaan Aset BUN', 'Y', 'Unit', 'Aset', NULL, NULL, NULL, NULL, NULL, NULL),
('UAL', 'Pengelolaan Pelaksanaan Anggaran dan Pembiayaan', 'Y', 'Dokumen', 'Kegiatan', NULL, NULL, NULL, NULL, NULL, NULL),
('UAM', 'Hasil Kelolaan Dana', 'Y', 'Rupiah', 'Hektar', 'Orang', 'UsahaMikro', 'milyar', NULL, NULL, NULL),
('UBA', 'Fasilitasi dan Pembinaan Pemerintah Daerah', 'Y', 'Daerah(Prov/Kab/Kota)', 'Provinsi', 'Kab/Kota', NULL, NULL, NULL, NULL, NULL),
('UBB', 'Fasilitasi dan Pembinaan Pemerintah Desa', 'Y', 'Desa', 'Desa/Kelurahan', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_rab_program`
--

CREATE TABLE `par_rab_program` (
  `program_id` varchar(10) NOT NULL,
  `nama_program` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_rab_program`
--

INSERT INTO `par_rab_program` (`program_id`, `nama_program`) VALUES
('CF', 'PROGRAM DUKUNGAN MANAJEMEN'),
('WA', 'PROGRAM PENYELENGGARAAN LEMBAGA LEGISLATIF DAN ALAT KELENGKAPAN');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_ro`
--

CREATE TABLE `par_ro` (
  `ro_id` varchar(50) NOT NULL,
  `nama_ro` text NOT NULL,
  `no_ro` varchar(10) DEFAULT NULL,
  `kegiatan_id` int(11) NOT NULL,
  `kro_id` varchar(10) NOT NULL,
  `biro_id` int(11) NOT NULL,
  `bagian_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_ro`
--

INSERT INTO `par_ro` (`ro_id`, `nama_ro`, `no_ro`, `kegiatan_id`, `kro_id`, `biro_id`, `bagian_id`) VALUES
('7983.AAA.001', 'RUU USUL DPD RI TUGAS KOMITE I', '001', 7983, 'AAA', 6, 19),
('7983.AAA.002', 'RUU USUL DPD RI BIDANG TUGAS KOMITE II', '002', 7983, 'AAA', 7, 24),
('7983.AAA.003', 'RUU USUL DPD RI TUGAS KOMITE III DPD RI', '003', 7983, 'AAA', 6, 20),
('7983.AAA.004', 'RUU USUL DPD RI BIDANG TUGAS KOMITE IV', '004', 7983, 'AAA', 7, 25),
('7983.AAA.005', 'RUU USUL DPD RI TUGAS PPUU', '005', 7983, 'AAA', 6, 21),
('7983.AAA.006', 'KONSEP RUU', '006', 7983, 'AAA', 6, 21),
('7983.AAA.007', 'RUU LAINNYA HASIL PERTIMBANGAN DPD RI BIDANG TUGAS KOMITE III', '007', 7983, 'AAA', 6, 20),
('7983.AAA.008', 'REKOMENDASI USUL PROLEGNAS', '008', 7983, 'AAA', 6, 21),
('7983.AAA.009', 'RUU HASIL PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE I DPD', '009', 7983, 'AAA', 6, 19),
('7983.AAA.010', 'RUU HASIL PENYUSUNAN PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE II DPD', '010', 7983, 'AAA', 7, 24),
('7983.AAA.011', 'RUU HASIL PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE III DPD', '011', 7983, 'AAA', 6, 20),
('7983.AAA.012', 'RUU HASIL PANDANGAN DAN PENDAPAT BIDANG TUGAS KOMITE IV', '012', 7983, 'AAA', 7, 25),
('7983.AAA.013', 'RUU APBN HASIL PERTIMBANGAN DPD RI', '013', 7983, 'AAA', 7, 25),
('7983.AAA.014', 'RUU LAINNYA HASIL PERTIMBANGAN DPD RI BIDANG TUGAS KOMITE IV', '014', 7983, 'AAA', 7, 25),
('7983.ABA.001', 'REKOMENDASI KEBIJAKAN DALAM PROGRAM PEMBANGUNAN DAN KEUANGAN NEGARA', '001', 7983, 'ABA', 7, 25),
('7983.ABC.001', 'Rekomendasi Kebijakan Atas Pengolahan Aspirasi Masyarakat dan Daerah', '001', 7983, 'ABC', 10, 40),
('7983.ABC.002', 'REKOMENDASI KEBIJAKAN ATAS PENYELENGGARAAN FUNGSI LEGISLASI DAN RUU DPD RI', '002', 7983, 'ABC', 9, 37),
('7983.ABC.003', 'REKOMENDASI KEBIJAKAN PANDANGAN DAN PENDAPAT ATAS RUU DARI PEMERINTAH DAN DPR', '003', 7983, 'ABC', 9, 37),
('7983.ABC.004', 'REKOMENDASI KEBIJAKAN PERTIMBANGAN DPD RI ATAS RUU LAINNYA', '004', 7983, 'ABC', 9, 37),
('7983.AΕA.001', 'MATERI PENGELOLAAN ASPIRASI MASYARAKAT DAN DAERAH', '001', 7983, 'AΕA', 10, 40),
('7983.BMB.001', 'KEGIATAN ANGGOTA DPD RI PADA MASA KEGIATAN DI DAERAH (MASA RESES)', '001', 7983, 'BMB', 10, 40),
('7983.BMB.002', 'KUNJUNGAN KERJA PERSEORANGAN DI DAERAH PEMILIHAN (KUNDAPIL) OLEH ANGGOTA DPD', '002', 7983, 'BMB', 10, 40),
('7983.PBC.001', 'Rekomendasi kebijakan atas tabulasi data Asmasda Legislasi RUU', '001', 7983, 'PBC', 10, 40),
('7983.PBC.002', 'Rekomendasi kebijakan atas tabulasi data Asmasda Legislasi Pandangan dan Pendapat atas RUU Usul Pemerintah dan DPR', '002', 7983, 'PBC', 10, 40),
('7983.PBC.003', 'Rekomendasi kebijakan atas tabulasi data Asmasda Legislasi Pertimbangan atas RUU APBN', '003', 7983, 'PBC', 10, 40),
('7983.PBC.004', 'Rekomendasi Kebijakan atas Tabulasi Data Asmasda Legislasi Pertimbangan atas RUU Lainnya', '004', 7983, 'PBC', 10, 40),
('7984.ABA.001', 'REKOMENDASI ATAS HASIL PEMERIKSAAN KEUANGAN NEGARA', '001', 7984, 'ABA', 7, 25),
('7984.ABC.001', 'REKOMENDASI HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU BIDANG TUGAS KOMITE I', '001', 7984, 'ABC', 6, 19),
('7984.ABC.002', 'REKOMENDASI HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UNDANG-UNDANG BIDANG TUGAS KOMITE II', '002', 7984, 'ABC', 7, 24),
('7984.ABC.003', 'Rekomendasi hasil pengawasan DPD RI atas pelaksanaan UU bidang tugas Komite III', '003', 7984, 'ABC', 6, 20),
('7984.ABC.004', 'REKOMENDASI HASIL PENGAWASAN DPD RI ATAS PELAKSANAAN UU TERTENTU BIDANG KOMITE IV', '004', 7984, 'ABC', 7, 25),
('7984.ABC.005', 'REKOMENDASI PERTIMBANGAN DPD RI DALAM PEMILIHAN CALON ANGGOTA BPK RI', '005', 7984, 'ABC', 7, 25),
('7984.ABC.006', 'REKOMENDASI ATAS HASIL PEMANTAUAN DAN EVALUASI RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH', '006', 7984, 'ABC', 6, 22),
('7984.ABC.007', 'REKOMENDASI ATAS PERMINTAAN DAERAH TENTANG PERMASALAHAN HUKUM DI DAERAH', '007', 7984, 'ABC', 6, 22),
('7984.ABC.008', 'REKOMENDASI ATAS PEMANTAUAN TERHADAP PELAKSANAAN UNDANG-UNDANG', '008', 7984, 'ABC', 6, 21),
('7984.ABC.009', 'REKOMENDASI ATAS HASIL PENINJAUAN TERHADAP PELAKSANAAN UNDANG - UNDANG', '009', 7984, 'ABC', 6, 21),
('7984.ABC.010', 'REKOMENDASI KEBIJAKAN ATAS PEMANTAUAN DAN PENINJAUAN UNDANG-UNDANG DAN KEBIJAKAN PEMERINTAH LAINNYA', '010', 7984, 'ABC', 9, 37),
('7984.ABC.011', 'REKOMENDASI ISU STRATEGIS HASIL PEMANTAUAN DAN EVALUASI RANCANGAN PERATURAN DAERAH DAN PERATURAN DAERAH', '011', 7984, 'ABC', 9, 37),
('7984.ABC.012', 'REKOMENDASI KEBIJAKAN PENGAWASAN DPD RI ATAS PELAKSANAAN UNDANG-UNDANG', '012', 7984, 'ABC', 9, 37),
('7984.ABC.013', 'REKOMENDASI ATAS HASIL MONITORING TINDAK LANJUT ATAS PEMANTAUAN DAN EVALUASI RANCANGAN PERDA DAN PERDA', '013', 7984, 'ABC', 6, 22),
('7984.AΕA.001', 'PERSIDANGAN DAN MATERI DALAM LINGKUP BIRO PERSIDANGAN I', '001', 7984, 'AΕA', 6, 41),
('7984.AΕA.002', 'LAPORAN MATERI PERSIDANGAN LINGKUP BIRO PERSIDANGAN II', '002', 7984, 'AΕA', 7, 42),
('7984.AΕA.003', 'LAPORAN RESOLUSI PERMASALAHAN DAERAH BIDANG TUGAS KOMITE I', '003', 7984, 'AΕA', 6, 19),
('7984.AΕA.004', 'RESOLUSI PERMASALAHAN DAERAH LINGKUP TUGAS KOMITE II', '004', 7984, 'AΕA', 7, 24),
('7984.AΕA.005', 'RESOLUSI PERMASALAHAN DAERAH LINGKUP TUGAS KOMITE III', '005', 7984, 'AΕA', 6, 20),
('7984.AΕA.006', 'RESOLUSI PERMASALAHAN DAERAH LINGKUP TUGAS KOMITE IV', '006', 7984, 'AΕA', 7, 25),
('7984.PBC.001', 'Rekomendasi Kebijakan atas Tabulasi Data Asmasda Pengawasan atas Pelaksanaan Undang-undang', '001', 7984, 'PBC', 10, 40),
('7984.PBC.002', 'Rekomendasi Kebijakan atas Tabulasi Data Asmada untuk Pemantauan dan Peninjauan', '002', 7984, 'PBC', 10, 40),
('7985.AAH.001', 'PERATURAN INTERNAL DPD RI', '001', 7985, 'AAH', 6, 21),
('7985.AAH.002', 'KEPUTUSAN BADAN KEHORMATAN ATAS DUGAAN PELANGGARAN TATA TERTIB DAN KODE ETIK YANG DILAKUKAN OLEH ANGGOTA DPD RI', '002', 7985, 'AAH', 7, 27),
('7985.AAH.003', 'PERATURAN DPD TENTANG TATA TERTIB/KODE ETIK/TATABERACARA BADAN KEHORMATAN', '003', 7985, 'AAH', 7, 27),
('7985.ABA.001', 'REKOMENDASI TINDAK LANJUT HASIL PEMERIKSAAN BPK RI YANG BERINDIKASI KERUGIAN NEGARA/DAERAH', '001', 7985, 'ABA', 7, 29),
('7985.ABC.001', 'REKOMENDASI ATAS TINDAK LANJUT PENGADUAN MASYARAKAT DAN PERMASALAHAN YANG DISAMPAIKAN PEMERINTAH DAERAH', '001', 7985, 'ABC', 7, 29),
('7985.ABL.001', 'KEBIJAKAN TATA KELOLA INTERNAL DEWAN', '001', 7985, 'ABL', 7, 28),
('7985.AEC.001', 'KERJA SAMA ANTAR PARLEMEN DAN LEMBAGA INTERNASIONAL', '001', 7985, 'AEC', 7, 23),
('7985.AΕA.001', 'LAPORAN PELAKSANAAN TUGAS PANMUS/PANSUS DPD RI', '001', 7985, 'AΕA', 7, 26),
('7985.BLA.001', 'SIDANG PARIPURNA BERSAMA DPD/DPR', '001', 7985, 'BLA', 7, 26),
('7986.AΕA.001', 'LAPORAN PELAKSANAAN PEMASYARAKatan PRODUK HUKUM DPD RI', '001', 7986, 'AΕA', 8, 34),
('7986.BMA.001', 'INFORMASI KEBIJAKAN HUKUM', '001', 7986, 'BMA', 9, 37);

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_satker`
--

CREATE TABLE `par_satker` (
  `satker_id` int(11) NOT NULL,
  `nama_satker` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_satker`
--

INSERT INTO `par_satker` (`satker_id`, `nama_satker`) VALUES
(452646, 'DEWAN PERWAKILAN DAERAH'),
(465224, 'SEKRETARIAT JENDERAL DPD RI');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_sub_komponen`
--

CREATE TABLE `par_sub_komponen` (
  `sub_komponen_id` varchar(150) NOT NULL,
  `komponen_id` varchar(100) NOT NULL,
  `kode_sub_komponen` varchar(10) NOT NULL,
  `nama_sub_komponen` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_sub_komponen`
--

INSERT INTO `par_sub_komponen` (`sub_komponen_id`, `komponen_id`, `kode_sub_komponen`, `nama_sub_komponen`) VALUES
('7983.AAA.001.051.A', '7983.AAA.001.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.001.051.B', '7983.AAA.001.051', 'B', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.001.051.C', '7983.AAA.001.051', 'C', 'Peer Review Draft RUU Usul DPD'),
('7983.AAA.001.051.D', '7983.AAA.001.051', 'D', 'Finalisasi Naskah Akademik'),
('7983.AAA.002.051.A', '7983.AAA.002.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.002.051.B', '7983.AAA.002.051', 'B', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.002.051.C', '7983.AAA.002.051', 'C', 'Peer Review Draft RUU Usul DPD'),
('7983.AAA.002.051.D', '7983.AAA.002.051', 'D', 'Finalisasi Naskah Akademik'),
('7983.AAA.003.051.A', '7983.AAA.003.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.003.051.B', '7983.AAA.003.051', 'B', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.003.051.C', '7983.AAA.003.051', 'C', 'Peer Review Draft RUU Usul DPD'),
('7983.AAA.003.051.D', '7983.AAA.003.051', 'D', 'Finalisasi Naskah Akademik'),
('7983.AAA.003.054.B', '7983.AAA.003.054', 'B', 'Telaah Materi RUU Usul Tugas Komite III'),
('7983.AAA.004.051.A', '7983.AAA.004.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.004.051.B', '7983.AAA.004.051', 'B', 'Preparasi Penyusunan Draft Naskah Akademik RUU'),
('7983.AAA.004.051.C', '7983.AAA.004.051', 'C', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.004.051.D', '7983.AAA.004.051', 'D', 'Peer Review Draft RUU Usul Inisiatif DPD'),
('7983.AAA.004.051.E', '7983.AAA.004.051', 'E', 'Finalisasi Naskah Akademik RUU Usul Inisiatif DPD'),
('7983.AAA.005.051.A', '7983.AAA.005.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.005.051.B', '7983.AAA.005.051', 'B', 'Pembahasan Kerangka Konseptual'),
('7983.AAA.005.051.C', '7983.AAA.005.051', 'C', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.005.051.D', '7983.AAA.005.051', 'D', 'Peer Review Draft Naskah Akademik RUU'),
('7983.AAA.005.051.E', '7983.AAA.005.051', 'E', 'Finalisasi Naskah Akademik RUU'),
('7983.AAA.006.051.A', '7983.AAA.006.051', 'A', 'Pembahasan Kerangka Konseptual'),
('7983.AAA.006.051.B', '7983.AAA.006.051', 'B', 'Penyusunan Tabulasi Daftar Inventarisasi Masalah (DIM) Harmonisasi'),
('7983.AAA.006.051.C', '7983.AAA.006.051', 'C', 'Pembahasan Daftar Inventarisasi Masalah (DIM) Harmonisasi'),
('7983.AAA.007.051.A', '7983.AAA.007.051', 'A', 'Preparasi Penyusunan Draft Hasil Pertimbangan DPD RI'),
('7983.AAA.008.051.A', '7983.AAA.008.051', 'A', 'Dukungan Tim Ahli'),
('7983.AAA.008.051.B', '7983.AAA.008.051', 'B', 'Pembahasan Kerangka Konseptual'),
('7983.AAA.008.051.C', '7983.AAA.008.051', 'C', 'Penelitian Empirik/Kunjungan ke Lapangan'),
('7983.AAA.008.051.D', '7983.AAA.008.051', 'D', 'Peer Review Draft Naskah Akademik Prolegnas Usul DPD RI'),
('7983.AAA.008.051.E', '7983.AAA.008.051', 'E', 'Finalisasi Naskah Akademik RUU'),
('7983.AAA.008.052.A', '7983.AAA.008.052', 'A', 'Pembahasan DIM'),
('7983.AAA.008.052.B', '7983.AAA.008.052', 'B', 'Panitia Kerja'),
('7983.AAA.008.052.C', '7983.AAA.008.052', 'C', 'Tim Perumus'),
('7983.AAA.008.052.D', '7983.AAA.008.052', 'D', 'Tim Sinkronisasi'),
('7983.AAA.008.053.A', '7983.AAA.008.053', 'A', 'Konsultasi Publik'),
('7983.AAA.008.053.B', '7983.AAA.008.053', 'B', 'Kunjungan Kerja'),
('7983.AAA.009.052.A', '7983.AAA.009.052', 'A', 'Kunjungan Kerja dalam rangka Penyusunan Inventarisasi Materi Bahan Pembahasan bersama DPR dan Presiden (Pemerintah)'),
('7983.AAA.009.052.B', '7983.AAA.009.052', 'B', 'Pembahasan DIM'),
('7983.AAA.009.052.C', '7983.AAA.009.052', 'C', 'Panitia Kerja'),
('7983.AAA.009.052.D', '7983.AAA.009.052', 'D', 'Tim Perumus'),
('7983.AAA.009.052.E', '7983.AAA.009.052', 'E', 'Tim Sinkronisasi'),
('7983.AAA.011.051.A', '7983.AAA.011.051', 'A', 'Inventarisasi Materi RUU Hasil Pandangan dan Pendapat Bidang Tugas Komite III DPD'),
('7983.AAA.011.052.A', '7983.AAA.011.052', 'A', 'Kunjungan Kerja dalam rangka Penyusunan Inventarisasi Materi Bahan Pembahasan bersama DPR dan Presiden (Pemerintah)'),
('7983.AAA.011.052.B', '7983.AAA.011.052', 'B', 'Panitia Kerja'),
('7983.AAA.011.052.C', '7983.AAA.011.052', 'C', 'Tim Perumus'),
('7983.AAA.011.052.D', '7983.AAA.011.052', 'D', 'Tim Sinkronisasi'),
('7983.AAA.012.053.A', '7983.AAA.012.053', 'A', 'Pembahasan DIM'),
('7983.AAA.012.053.B', '7983.AAA.012.053', 'B', 'Panitia Kerja'),
('7983.AAA.012.053.C', '7983.AAA.012.053', 'C', 'Tim Perumus'),
('7983.AAA.012.053.D', '7983.AAA.012.053', 'D', 'Tim Sinkronisasi'),
('7983.AAA.013.051.A', '7983.AAA.013.051', 'A', 'FGD Inventarisasi Program Prioritas Kewilayahan/Kedaerahan dalam rangka Perencanaan Pembangunan Nasional pada RAPBN (Pra RAPBN )'),
('7983.AAA.013.051.B', '7983.AAA.013.051', 'B', 'Kunjungan Kerja Penyusunan Pertimbangan DPD RI atas RUU APBN/APBNP dan RUU Pertanggungjawaban Pelaksanaan APBN'),
('7983.AAA.013.051.C', '7983.AAA.013.051', 'C', 'Finalisasi Penyusunan Pertimbangan DPD atas RUU APBN/APBNP dan RUU Pertanggungjawaban Pelaksanaan APBN'),
('7983.ABA.001.051.A', '7983.ABA.001.051', 'A', 'Focus Group Discussion (FGD) dalam rangka Inventarisasi Materi'),
('7983.ABA.001.051.C', '7983.ABA.001.051', 'C', 'Rapat dengan Kementerian PPN/Bappenas dalam rangka Penyusunan Rekomendasi Kebijakan terkait Rencana Kerja Pemerintah'),
('7983.AΕA.001.051.A', '7983.AΕA.001.051', 'A', 'Pendampingan Kegiatan Anggota dalam rangka Penyerapan Aspirasi Masyarakat dan Daerah'),
('7983.AΕA.001.051.B', '7983.AΕA.001.051', 'B', 'Pendampingan Kegiatan Alat Kelengkapan dalam rangka Penyerapan Aspirasi Masyarakat dan Daerah di Ibu Kota Provinsi'),
('7983.AΕA.001.051.C', '7983.AΕA.001.051', 'C', 'Pertemuan/Jamuan/Konsultasi Anggota DPD dengan Unsur Masyarakat Daerah dan Konstituen'),
('7983.AΕA.001.053.A', '7983.AΕA.001.053', 'A', 'Koordinasi Hasil Pengolahan Asmasda'),
('7983.BMB.001.051.A', '7983.BMB.001.051', 'A', 'Kegiatan Anggota DPD RI pada Masa Kegiatan di Daerah (Masa Reses)'),
('7983.BMB.002.051.A', '7983.BMB.002.051', 'A', 'Kunjungan Kerja Perseorangan di Daerah Pemilihan (Kundapil)'),
('7984.ABA.001.051.A', '7984.ABA.001.051', 'A', 'Kunjungan Kerja dalam rangka Rapat Konsultasi dengan BPK Perwakilan di Provinsi'),
('7984.ABA.001.051.B', '7984.ABA.001.051', 'B', 'Rapat Konsultasi dengan BPK RI dalam rangka Tindak Lanjut atas Hasil Pemeriksaan Keuangan Negara'),
('7984.ABC.001.051.A', '7984.ABC.001.051', 'A', 'Kunjungan Kerja Dalam Rangka Inventarisasi Materi Pengawasan Atas Pelaksanaan UU'),
('7984.ABC.001.051.B', '7984.ABC.001.051', 'B', 'Telaah Materi Pengawasan Atas Pelaksanaan UU'),
('7984.ABC.001.052.A', '7984.ABC.001.052', 'A', 'Preparasi Penyusunan Draft Hasil Pengawasan DPD RI Atas Pelaksanaan UU Tertentu'),
('7984.ABC.001.052.B', '7984.ABC.001.052', 'B', 'Finalisasi Penyusunan Hasil Pengawasan DPD Ri Atas Pelaksanaan UU Tertentu'),
('7984.ABC.001.055.A', '7984.ABC.001.055', 'A', 'Penyusunan Buku Laporan Kinerja Komite I'),
('7984.ABC.001.055.B', '7984.ABC.001.055', 'B', 'Kegiatan Penyusunan Rencana Kerja Tahun 2024'),
('7984.ABC.002.054.A', '7984.ABC.002.054', 'A', 'Penyusunan Buku Laporan Kinerja'),
('7984.ABC.003.051.A', '7984.ABC.003.051', 'A', 'Kunjungan Kerja Dalam Rangka Inventarisasi Materi Pengawasan Atas Pelaksanaan UU'),
('7984.ABC.003.052.A', '7984.ABC.003.052', 'A', 'Pra Haji'),
('7984.ABC.003.052.B', '7984.ABC.003.052', 'B', 'Pelaksanaan'),
('7984.ABC.003.054.A', '7984.ABC.003.054', 'A', 'Telaah Materi Pengawasan Atas Pelaksanaan UU'),
('7984.ABC.003.054.B', '7984.ABC.003.054', 'B', 'Preparasi Penyusunan Draft Hasil Pengawasan DPD RI Atas Pelaksanaan UU Tertentu'),
('7984.ABC.003.056.B', '7984.ABC.003.056', 'B', 'Kegiatan Penyusunan Rencana Kerja Tahun 2027'),
('7984.ABC.004.051.A', '7984.ABC.004.051', 'A', 'Preparasi Penyusunan Draft Hasil Pengawasan atas Pelaksanaan UU Tertentu'),
('7984.ABC.004.051.B', '7984.ABC.004.051', 'B', 'Kunjungan Kerja Inventarisasi Materi'),
('7984.ABC.004.053.A', '7984.ABC.004.053', 'A', 'Penyusunan Buku Laporan Kinerja Komite IV Tahun 2023'),
('7984.ABC.004.053.B', '7984.ABC.004.053', 'B', 'Penyusunan Buletin Komite IV'),
('7984.ABC.006.051.A', '7984.ABC.006.051', 'A', 'Pembahasan Kerangka Konseptual'),
('7984.ABC.006.051.B', '7984.ABC.006.051', 'B', 'Inventarisasi Materi'),
('7984.ABC.006.051.C', '7984.ABC.006.051', 'C', 'Dialog Nasional'),
('7984.ABC.006.051.D', '7984.ABC.006.051', 'D', 'Perumusan'),
('7984.ABC.006.052.A', '7984.ABC.006.052', 'A', 'Dialog BULD ke Daerah'),
('7984.ABC.006.052.B', '7984.ABC.006.052', 'B', 'Pengumpulan Data'),
('7984.ABC.006.053.A', '7984.ABC.006.053', 'A', 'Pendalaman Materi'),
('7984.ABC.006.053.B', '7984.ABC.006.053', 'B', 'Penelaahan dan analisis hasil Pemantauan dan Evaluasi Rancangan Perda dan Perda'),
('7984.ABC.006.055.A', '7984.ABC.006.055', 'A', 'Konsultasi Publik'),
('7984.ABC.006.055.B', '7984.ABC.006.055', 'B', 'Penyempurnaan draft Hasil Pemantauan dan Evaluasi Rancangan Perda dan Perda'),
('7984.ABC.006.057.A', '7984.ABC.006.057', 'A', 'Perumusan Konsepsi'),
('7984.ABC.006.057.B', '7984.ABC.006.057', 'B', 'Penyusunan Materi'),
('7984.ABC.007.052.A', '7984.ABC.007.052', 'A', 'Temu Konsultasi Legislasi Pusat-Daerah'),
('7984.ABC.007.052.B', '7984.ABC.007.052', 'B', 'Dialog Daerah'),
('7984.ABC.008.051.A', '7984.ABC.008.051', 'A', 'Pembahasan Kerangka Konseptual'),
('7984.ABC.008.051.B', '7984.ABC.008.051', 'B', 'Telaah Sejawat/Peer Review'),
('7984.ABC.008.051.C', '7984.ABC.008.051', 'C', 'Penelitian Empirik/Kunjungan ke Lapangan Inventarisasi Undang-Undang'),
('7984.ABC.008.052.A', '7984.ABC.008.052', 'A', 'Konsultasi Publik'),
('7984.ABC.009.051.A', '7984.ABC.009.051', 'A', 'Kunjungan Kerja dalam rangka Pemantauan Undang-Undang'),
('7984.ABC.009.052.A', '7984.ABC.009.052', 'A', 'Penyusunan Rekomendasi'),
('7984.AΕA.001.051.A', '7984.AΕA.001.051', 'A', 'Koordinasi dan Evaluasi Biro Persidangan I'),
('7984.AΕA.001.051.B', '7984.AΕA.001.051', 'B', 'Rapat Internal Biro Persidangan I'),
('7984.AΕA.001.051.C', '7984.AΕA.001.051', 'C', 'Sidang/Rapat Pleno/Tim Kerja'),
('7984.AΕA.002.051.A', '7984.AΕA.002.051', 'A', 'Rapat Koordinasi dalam rangka evaluasi program kerja Biro Persidangan II'),
('7984.AΕA.002.051.B', '7984.AΕA.002.051', 'B', 'Rapat Biro Persidangan II'),
('7984.AΕA.002.051.C', '7984.AΕA.002.051', 'C', 'Sidang/Rapat Pleno/Tim Kerja'),
('7985.AAH.001.051.A', '7985.AAH.001.051', 'A', 'Perumusan'),
('7985.AAH.001.051.B', '7985.AAH.001.051', 'B', 'Finalisasi Draft Peraturan DPD RI'),
('7985.AAH.002.051.A', '7985.AAH.002.051', 'A', 'Persiapan/Rapat'),
('7985.AAH.002.051.B', '7985.AAH.002.051', 'B', 'Tinjauan Lapangan Pra-Penyelidikan Pimpinan Badan Kehormatan'),
('7985.AAH.002.051.C', '7985.AAH.002.051', 'C', 'Penyelidikan dan Verifikasi Faktual Dugaan Pelanggaran Tata Tertib dan Kode Etik'),
('7985.AAH.002.051.D', '7985.AAH.002.051', 'D', 'Finalisasi'),
('7985.AAH.003.051.A', '7985.AAH.003.051', 'A', 'Focus Group Discussion (FGD)'),
('7985.AAH.003.051.B', '7985.AAH.003.051', 'B', 'Rapat/ Pembahasan Hasil DIM'),
('7985.AAH.003.051.C', '7985.AAH.003.051', 'C', 'Finalisasi'),
('7985.ABA.001.051.A', '7985.ABA.001.051', 'A', 'Inventarisasi Hasil Pemeriksaan BPK yang Berindikasi Kerugian Negara/Daerah'),
('7985.ABA.001.051.B', '7985.ABA.001.051', 'B', 'Kunjungan Kerja Hasil Pemeriksaan BPK yang Berindikasi Kerugian Negara/Daerah'),
('7985.ABC.001.051.A', '7985.ABC.001.051', 'A', 'Inventarisasi Permasalahan Pengaduan Masyarakat dan Permasalahan yang Disampaikan Pemerintah Daerah'),
('7985.ABC.001.051.B', '7985.ABC.001.051', 'B', 'Kunjungan Kerja Permasalahan Pengaduan Masyarakat dan Permasalahan yang Disampaikan Pemerintah Daerah'),
('7985.ABL.001.052.A', '7985.ABL.001.052', 'A', 'Penyusunan Arah Kebijakan Anggaran Dan Kerumahtanggaan DPD RI & Penyusunan Kebijakan Tentang Pengelolaan Kantor DPD RI Di Ibukota Provinsi'),
('7985.ABL.001.052.B', '7985.ABL.001.052', 'B', 'Rekomendasi Pengelolaan Anggaran DPD RI Berdasarkan Hasil Studi Pengelolaan Anggaran'),
('7985.AEC.001.052.A', '7985.AEC.001.052', 'A', 'Penyusunan Potensi Daerah bersama Pemerintah Daerah dan Mitra Terkait'),
('7985.AEC.001.052.B', '7985.AEC.001.052', 'B', 'Sinergitas Kerja Sama BKSP DPD RI dengan Perwakilan Negara Sahabat dan Stake Holders Terkait'),
('7985.AΕA.001.051.A', '7985.AΕA.001.051', 'A', 'Focus Group Discussion (FGD)'),
('7985.AΕA.001.051.B', '7985.AΕA.001.051', 'B', 'Finalisasi'),
('7985.AΕA.001.051.C', '7985.AΕA.001.051', 'C', 'Penyusunan Buku Kompilasi Jadwal Persidangan DPD RI'),
('7985.AΕA.001.052.A', '7985.AΕA.001.052', 'A', 'Kunjungan kerja dalam rangka inventarisasi'),
('7985.AΕA.001.052.B', '7985.AΕA.001.052', 'B', 'Focus Group Discussion (FGD)'),
('7985.AΕA.001.052.C', '7985.AΕA.001.052', 'C', 'Finalisasi'),
('7986.AΕA.001.051.A', '7986.AΕA.001.051', 'A', 'Kunjungan Kerja Ketua DPD RI bersama Anggota DPD'),
('7986.AΕA.001.051.B', '7986.AΕA.001.051', 'B', 'Kunjungan Kerja Wakil Ketua DPD RI Bidang I bersama Anggota DPD'),
('7986.AΕA.001.051.C', '7986.AΕA.001.051', 'C', 'Kunjungan Kerja Wakil Ketua DPD RI Bidang II bersama Anggota DPD'),
('7986.AΕA.001.051.D', '7986.AΕA.001.051', 'D', 'Kunjungan Kerja Wakil Ketua DPD RI Bidang III bersama Anggota DPD'),
('7986.AΕA.001.051.E', '7986.AΕA.001.051', 'E', 'FGD Ketua DPD RI dalam rangka pelaksanaan dan Pemasyarakatan Keputusan DPD RI'),
('7986.AΕA.001.051.F', '7986.AΕA.001.051', 'F', 'FGD Wakil Ketua DPD RI Bidang I dalam rangka pelaksanaan dan Pemasyarakatan Keputusan DPD RI'),
('7986.AΕA.001.051.G', '7986.AΕA.001.051', 'G', 'FGD Wakil Ketua DPD RI Bidang II dalam rangka pelaksanaan dan Pemasyarakatan Keputusan DPD RI'),
('7986.AΕA.001.051.H', '7986.AΕA.001.051', 'H', 'FGD Wakil Ketua DPD RI Bidang III dalam rangka pelaksanaan dan Pemasyarakatan Keputusan DPD RI'),
('7986.AΕA.001.052.A', '7986.AΕA.001.052', 'A', 'Konsultasi ke Pemerintah Daerah dalam rangka Persiapan Kunjungan Kerja Pimpinan DPD RI'),
('7986.AΕA.001.052.B', '7986.AΕA.001.052', 'B', 'Rapat Koordinasi Biro Sekretariat Pimpinan'),
('7986.AΕA.001.052.C', '7986.AΕA.001.052', 'C', 'Dukungan Kesekretariatan terhadap Pimpinan DPD RI dan Pimpinan Sekretariat Jenderal DPD RI'),
('7986.AΕA.001.053.A', '7986.AΕA.001.053', 'A', 'Fasilitasi Kegiatan Penerimaan Kunjungan Stakeholder/Konstituen DPD RI'),
('7986.AΕA.001.053.B', '7986.AΕA.001.053', 'B', 'Fasilitasi Kegiatan Penerimaan Kunjungan Stakeholder/Konstituen Wakil Ketua DPD RI Bidang I'),
('7986.AΕA.001.053.C', '7986.AΕA.001.053', 'C', 'Fasilitasi Kegiatan Penerimaan Kunjungan Stakeholder/Konstituen Wakil Ketua DPD RI Bidang II'),
('7986.AΕA.001.053.D', '7986.AΕA.001.053', 'D', 'Fasilitasi Kegiatan Penerimaan Kunjungan Stakeholder/Konstituen Wakil Ketua DPD RI Bidang III'),
('7986.AΕA.001.053.E', '7986.AΕA.001.053', 'E', 'Fasilitasi Kegiatan Publikasi Anggota DPD RI'),
('7986.AΕA.001.054.A', '7986.AΕA.001.054', 'A', 'Executive Brief'),
('7986.AΕA.001.054.B', '7986.AΕA.001.054', 'B', 'Konsolidasi Pimpinan DPD RI Bersama Anggota DPD RI Sub Wilayah'),
('7986.AΕA.001.054.C', '7986.AΕA.001.054', 'C', 'Koordinasi Akselerasi Rencana Kerja Pimpinan DPD RI'),
('7986.BMA.001.051.A', '7986.BMA.001.051', 'A', 'Workshop Pengelolaan JDIH'),
('7986.BMA.001.051.B', '7986.BMA.001.051', 'B', 'Koordinasi Dengan Pemerintah Daerah'),
('7986.BMA.001.051.C', '7986.BMA.001.051', 'C', 'Sosialisasi JDIH'),
('7986.BMA.001.051.D', '7986.BMA.001.051', 'D', 'Seminar Nasional Penguatan JDIH'),
('7986.BMA.001.051.E', '7986.BMA.001.051', 'E', 'Pengundangan dan Penerjemahan Produk Hukum DPD'),
('7986.BMA.001.051.F', '7986.BMA.001.051', 'F', 'Pengelolaan Indeks Reformasi Hukum'),
('7986.BMA.001.051.G', '7986.BMA.001.051', 'G', 'Evaluasi JDIH'),
('7986.BMA.001.052.A', '7986.BMA.001.052', 'A', 'Penyusunan Jurnal Ilmiah'),
('7986.BMA.001.052.B', '7986.BMA.001.052', 'B', 'Penyusunan Buletin JDIH DPD'),
('7986.BMA.001.052.C', '7986.BMA.001.052', 'C', 'Penyusunan Anotasi JDIH DPD RI'),
('7986.BMA.001.052.D', '7986.BMA.001.052', 'D', 'Penyusunan Laporan Pelaksanaan Tugas Sekretariat Jenderal DPD RI dan Laporan IRH');

-- --------------------------------------------------------

--
-- Struktur dari tabel `par_sub_komponen_2`
--

CREATE TABLE `par_sub_komponen_2` (
  `sub_komponen_2_id` varchar(200) NOT NULL,
  `sub_komponen_id` varchar(150) NOT NULL,
  `kode_sub_komponen_2` varchar(10) NOT NULL,
  `nama_sub_komponen_2` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `par_sub_komponen_2`
--

INSERT INTO `par_sub_komponen_2` (`sub_komponen_2_id`, `sub_komponen_id`, `kode_sub_komponen_2`, `nama_sub_komponen_2`) VALUES
('7983.BMB.001.051.A.1', '7983.BMB.001.051.A', '1', 'Masa Kegiatan di Daerah (MKD) III TS 2025-2026'),
('7983.BMB.001.051.A.2', '7983.BMB.001.051.A', '2', 'Masa Kegiatan di Daerah (MKD) IV TS 2025-2026'),
('7983.BMB.001.051.A.3', '7983.BMB.001.051.A', '3', 'Masa Kegiatan di Daerah (MKD) V TS 2025-2026'),
('7983.BMB.001.051.A.4', '7983.BMB.001.051.A', '4', 'Masa Kegiatan di Daerah (MKD) I TS 2026-2027'),
('7983.BMB.001.051.A.5', '7983.BMB.001.051.A', '5', 'Masa Kegiatan di Daerah (MKD) II TS 2026-2027'),
('7983.BMB.002.051.A.1', '7983.BMB.002.051.A', '1', 'Biaya Dukungan Kegiatan dan Perjalanan Dinas Kunjungan Kerja Perseorangan di Daerah Pemilihan'),
('7986.AΕA.001.051.A.1', '7986.AΕA.001.051.A', '1', 'Kunjungan Kerja Dalam Negeri'),
('7986.AΕA.001.051.B.1', '7986.AΕA.001.051.B', '1', 'Kunjungan Kerja Dalam Negeri'),
('7986.AΕA.001.051.C.1', '7986.AΕA.001.051.C', '1', 'Kunjungan Kerja Dalam Negeri'),
('7986.AΕA.001.051.D.1', '7986.AΕA.001.051.D', '1', 'Kunjungan Kerja Dalam Negeri');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rab_detail`
--

CREATE TABLE `rab_detail` (
  `id_rab_detail` int(11) NOT NULL,
  `rab_entry_id` int(11) NOT NULL,
  `parent_detail_id` int(11) DEFAULT NULL,
  `revisi_ke` int(11) NOT NULL DEFAULT 0,
  `uraian_pekerjaan` text NOT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `harga_satuan_revisi` decimal(15,2) DEFAULT NULL,
  `total_biaya` decimal(18,2) DEFAULT NULL,
  `total_biaya_revisi` decimal(18,2) DEFAULT NULL,
  `volume_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`volume_data`)),
  `volume_data_revisi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`volume_data_revisi`)),
  `catatan` text DEFAULT NULL,
  `status_revisi` enum('ORIGINAL','REVISI','DISETUJUI','DITOLAK') NOT NULL DEFAULT 'ORIGINAL',
  `catatan_revisi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rab_detail`
--

INSERT INTO `rab_detail` (`id_rab_detail`, `rab_entry_id`, `parent_detail_id`, `revisi_ke`, `uraian_pekerjaan`, `harga_satuan`, `harga_satuan_revisi`, `total_biaya`, `total_biaya_revisi`, `volume_data`, `volume_data_revisi`, `catatan`, `status_revisi`, `catatan_revisi`) VALUES
(445, 67, NULL, 0, 'tes', 1000.00, NULL, 2000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(448, 68, NULL, 0, 'panitia', NULL, NULL, 0.00, NULL, '[]', NULL, NULL, 'ORIGINAL', NULL),
(449, 68, 448, 0, 'tes2', 10000.00, NULL, 20000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(450, 68, 448, 0, 'tes3', 10000.00, NULL, 20000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(451, 68, 448, 0, 'tes4', 20000.00, NULL, 40000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(452, 69, NULL, 0, 'panitia', NULL, NULL, 0.00, NULL, '[]', NULL, NULL, 'ORIGINAL', NULL),
(453, 69, 452, 0, 'tester', 10000.00, NULL, 20000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(454, 69, 452, 0, 'teter2', 20000.00, NULL, 40000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(455, 69, 452, 0, 'test_1', NULL, NULL, 0.00, NULL, '[]', NULL, NULL, 'ORIGINAL', NULL),
(456, 69, 455, 0, '1', 100000.00, NULL, 600000.00, NULL, '[{\"volume\":\"2\",\"satuan\":\"a\"},{\"volume\":\"3\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(457, 69, 455, 0, '2', 10000.00, NULL, 20000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(458, 69, 455, 0, '3', 100000.00, NULL, 200000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"2\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(460, 70, NULL, 0, 'tes lagi aja', 100000.00, NULL, 300000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"},{\"volume\":\"3\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(461, 70, NULL, 0, 'a', 1.00, NULL, 1.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"a\"}]', NULL, NULL, 'ORIGINAL', NULL),
(462, 70, NULL, 0, 'b', 1000.00, NULL, 1000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"b\"}]', NULL, NULL, 'ORIGINAL', NULL),
(463, 71, NULL, 0, 'mana', NULL, NULL, 0.00, NULL, '[]', NULL, NULL, 'ORIGINAL', NULL),
(464, 71, 463, 0, 'h', 1000000.00, NULL, 1000000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"h\"}]', NULL, NULL, 'ORIGINAL', NULL),
(465, 71, 463, 0, 'm', 100000.00, NULL, 100000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"m\"}]', NULL, NULL, 'ORIGINAL', NULL),
(466, 71, 463, 0, 'g', NULL, NULL, 0.00, NULL, '[]', NULL, NULL, 'ORIGINAL', NULL),
(467, 71, 466, 0, 'i', 10000.00, NULL, 10000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"i\"}]', NULL, NULL, 'ORIGINAL', NULL),
(468, 71, 466, 0, 'o', 10000.00, NULL, 10000.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"o\"}]', NULL, NULL, 'ORIGINAL', NULL),
(469, 71, 466, 0, 'k', 111111.00, NULL, 111111.00, NULL, '[{\"volume\":\"1\",\"satuan\":\"k\"}]', NULL, NULL, 'ORIGINAL', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rab_entry`
--

CREATE TABLE `rab_entry` (
  `id_rab_entry` int(11) NOT NULL,
  `ro_id` varchar(50) NOT NULL,
  `komponen_id` varchar(100) DEFAULT NULL,
  `sub_komponen_id` varchar(150) DEFAULT NULL,
  `sub_komponen_2_id` varchar(200) DEFAULT NULL,
  `id_akun` int(11) NOT NULL,
  `kode_akun` varchar(20) NOT NULL,
  `memiliki_revisi` tinyint(1) NOT NULL DEFAULT 0,
  `status_anggaran` enum('DRAFT','MENUNGGU_VERIFIKASI','DISETUJUI','DITOLAK','REVISI_DRAFT','REVISI_MENUNGGU_VERIFIKASI','REVISI_DISETUJUI','REVISI_DITOLAK') NOT NULL DEFAULT 'DRAFT',
  `tahun_anggaran` year(4) NOT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rab_entry`
--

INSERT INTO `rab_entry` (`id_rab_entry`, `ro_id`, `komponen_id`, `sub_komponen_id`, `sub_komponen_2_id`, `id_akun`, `kode_akun`, `memiliki_revisi`, `status_anggaran`, `tahun_anggaran`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(67, '7983.AAA.001', '7983.AAA.001.051', '7983.AAA.001.051.A', NULL, 2, '521213', 0, 'DISETUJUI', '2025', 20, '2025-09-29 09:02:08', '2025-09-29 09:11:01'),
(68, '7983.AAA.001', '7983.AAA.001.051', '7983.AAA.001.051.B', NULL, 1, '521211', 0, 'DISETUJUI', '2025', 20, '2025-09-29 09:02:36', '2025-09-29 09:11:01'),
(69, '7983.AAA.001', '7983.AAA.001.051', '7983.AAA.001.051.B', NULL, 10, '524119', 0, 'DISETUJUI', '2025', 20, '2025-09-29 09:04:57', '2025-09-29 09:11:01'),
(70, '7983.AAA.002', '7983.AAA.002.051', '7983.AAA.002.051.A', NULL, 1, '521211', 0, 'DISETUJUI', '2025', 25, '2025-09-29 09:07:03', '2025-09-29 09:11:04'),
(71, '7983.AAA.002', '7983.AAA.002.051', '7983.AAA.002.051.B', NULL, 10, '524119', 0, 'DISETUJUI', '2025', 25, '2025-09-29 09:09:23', '2025-09-29 09:11:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` varchar(50) NOT NULL,
  `biro_id` int(11) DEFAULT NULL,
  `bagian_id` int(11) DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `last_failed_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `user_role`, `biro_id`, `bagian_id`, `failed_login_attempts`, `last_failed_login`) VALUES
(1, 'admin', '$2y$10$FfvhxDSptTtMzX6TWwyhnePtfYeP92tcdrGONBWZpDMRin0iwgqsS', 'admin', NULL, NULL, 0, NULL),
(2, 'ortala', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 1, 1, 0, NULL),
(3, 'AKK', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 1, 2, 0, NULL),
(4, 'psdm', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 1, 3, 0, NULL),
(5, 'hukum', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 1, 4, 0, NULL),
(6, 'perencanaan', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'admin', 2, 5, 0, NULL),
(7, 'gaji', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 2, 6, 0, NULL),
(8, 'perben', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 2, 7, 0, NULL),
(9, 'aklap', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 2, 8, 0, NULL),
(10, 'bpsi', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 3, 9, 0, NULL),
(11, 'risalah', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 3, 10, 0, NULL),
(12, 'kpp', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 3, 11, 0, NULL),
(13, 'pbmn', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 4, 12, 0, NULL),
(14, 'pemeliharaan', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 4, 13, 0, NULL),
(15, 'layanan', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 4, 14, 0, NULL),
(16, 'pamdal', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 4, 15, 0, NULL),
(17, 'protokol', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 5, 16, 0, NULL),
(18, 'humas', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 5, 17, 0, NULL),
(19, 'media', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 5, 18, 0, NULL),
(20, 'komite_1', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 19, 0, NULL),
(21, 'komite_3', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 20, 0, NULL),
(22, 'ppuu', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 21, 0, NULL),
(23, 'buld', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 22, 0, NULL),
(24, 'bksp', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 23, 0, NULL),
(25, 'komite_2', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 24, 0, NULL),
(26, 'komite_4', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 25, 0, NULL),
(27, 'panmus', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 26, 0, NULL),
(28, 'bk', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 27, 0, NULL),
(29, 'purt', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 28, 0, NULL),
(30, 'bap', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 29, 0, NULL),
(31, 'set_ketua', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 8, 30, 0, NULL),
(32, 'set_waka_1', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 8, 31, 0, NULL),
(33, 'set_waka_2', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 8, 32, 0, NULL),
(34, 'set_waka_3', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 8, 33, 0, NULL),
(35, 'tu_setpim', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 8, 34, 0, NULL),
(36, 'perancang', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 9, 35, 0, NULL),
(37, 'jdih', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 9, 36, 0, NULL),
(38, 'tu_pusperjakum', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 9, 37, 0, NULL),
(39, 'asmasda', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 10, 38, 0, NULL),
(40, 'agpusda', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 10, 39, 0, NULL),
(41, 'tu_puskadaran', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 10, 40, 0, NULL),
(42, 'tu_rosid_1', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 6, 41, 0, NULL),
(43, 'tu_rosid_2', '$2y$10$iAoSVAM3IZZaODVia0BKHuVv4ynDxSoGeiNfrBpJKDXGo9OBUn6Ry', 'user', 7, 42, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `par_kode_akun`
--
ALTER TABLE `par_kode_akun`
  ADD PRIMARY KEY (`id_akun`),
  ADD UNIQUE KEY `kode_akun_unik` (`kode_akun`);

--
-- Indeks untuk tabel `par_komponen`
--
ALTER TABLE `par_komponen`
  ADD PRIMARY KEY (`komponen_id`),
  ADD KEY `ro_id` (`ro_id`);

--
-- Indeks untuk tabel `par_rab_bagian`
--
ALTER TABLE `par_rab_bagian`
  ADD PRIMARY KEY (`bagian_id`),
  ADD KEY `biro_id` (`biro_id`);

--
-- Indeks untuk tabel `par_rab_biro`
--
ALTER TABLE `par_rab_biro`
  ADD PRIMARY KEY (`biro_id`);

--
-- Indeks untuk tabel `par_rab_kegiatan`
--
ALTER TABLE `par_rab_kegiatan`
  ADD PRIMARY KEY (`kegiatan_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `satker_id` (`satker_id`);

--
-- Indeks untuk tabel `par_rab_kro`
--
ALTER TABLE `par_rab_kro`
  ADD PRIMARY KEY (`kro_id`);

--
-- Indeks untuk tabel `par_rab_program`
--
ALTER TABLE `par_rab_program`
  ADD PRIMARY KEY (`program_id`);

--
-- Indeks untuk tabel `par_ro`
--
ALTER TABLE `par_ro`
  ADD PRIMARY KEY (`ro_id`),
  ADD KEY `kegiatan_id` (`kegiatan_id`),
  ADD KEY `kro_id` (`kro_id`),
  ADD KEY `biro_id` (`biro_id`),
  ADD KEY `bagian_id` (`bagian_id`);

--
-- Indeks untuk tabel `par_satker`
--
ALTER TABLE `par_satker`
  ADD PRIMARY KEY (`satker_id`);

--
-- Indeks untuk tabel `par_sub_komponen`
--
ALTER TABLE `par_sub_komponen`
  ADD PRIMARY KEY (`sub_komponen_id`),
  ADD KEY `komponen_id` (`komponen_id`);

--
-- Indeks untuk tabel `par_sub_komponen_2`
--
ALTER TABLE `par_sub_komponen_2`
  ADD PRIMARY KEY (`sub_komponen_2_id`),
  ADD KEY `sub_komponen_id` (`sub_komponen_id`);

--
-- Indeks untuk tabel `rab_detail`
--
ALTER TABLE `rab_detail`
  ADD PRIMARY KEY (`id_rab_detail`),
  ADD KEY `rab_entry_id` (`rab_entry_id`),
  ADD KEY `idx_parent` (`parent_detail_id`),
  ADD KEY `idx_status_revisi` (`status_revisi`);

--
-- Indeks untuk tabel `rab_entry`
--
ALTER TABLE `rab_entry`
  ADD PRIMARY KEY (`id_rab_entry`),
  ADD KEY `ro_id` (`ro_id`),
  ADD KEY `komponen_id` (`komponen_id`),
  ADD KEY `sub_komponen_id` (`sub_komponen_id`),
  ADD KEY `sub_komponen_2_id` (`sub_komponen_2_id`),
  ADD KEY `id_akun` (`id_akun`),
  ADD KEY `idx_memiliki_revisi` (`memiliki_revisi`),
  ADD KEY `idx_status_anggaran` (`status_anggaran`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `biro_id` (`biro_id`),
  ADD KEY `bagian_id` (`bagian_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `par_kode_akun`
--
ALTER TABLE `par_kode_akun`
  MODIFY `id_akun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `par_rab_bagian`
--
ALTER TABLE `par_rab_bagian`
  MODIFY `bagian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `par_rab_biro`
--
ALTER TABLE `par_rab_biro`
  MODIFY `biro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `rab_detail`
--
ALTER TABLE `rab_detail`
  MODIFY `id_rab_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=470;

--
-- AUTO_INCREMENT untuk tabel `rab_entry`
--
ALTER TABLE `rab_entry`
  MODIFY `id_rab_entry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `par_komponen`
--
ALTER TABLE `par_komponen`
  ADD CONSTRAINT `par_komponen_ibfk_1` FOREIGN KEY (`ro_id`) REFERENCES `par_ro` (`ro_id`);

--
-- Ketidakleluasaan untuk tabel `par_rab_bagian`
--
ALTER TABLE `par_rab_bagian`
  ADD CONSTRAINT `par_rab_bagian_ibfk_1` FOREIGN KEY (`biro_id`) REFERENCES `par_rab_biro` (`biro_id`);

--
-- Ketidakleluasaan untuk tabel `par_rab_kegiatan`
--
ALTER TABLE `par_rab_kegiatan`
  ADD CONSTRAINT `par_rab_kegiatan_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `par_rab_program` (`program_id`),
  ADD CONSTRAINT `par_rab_kegiatan_ibfk_2` FOREIGN KEY (`satker_id`) REFERENCES `par_satker` (`satker_id`);

--
-- Ketidakleluasaan untuk tabel `par_ro`
--
ALTER TABLE `par_ro`
  ADD CONSTRAINT `par_ro_ibfk_1` FOREIGN KEY (`kegiatan_id`) REFERENCES `par_rab_kegiatan` (`kegiatan_id`),
  ADD CONSTRAINT `par_ro_ibfk_2` FOREIGN KEY (`kro_id`) REFERENCES `par_rab_kro` (`kro_id`),
  ADD CONSTRAINT `par_ro_ibfk_3` FOREIGN KEY (`biro_id`) REFERENCES `par_rab_biro` (`biro_id`),
  ADD CONSTRAINT `par_ro_ibfk_4` FOREIGN KEY (`bagian_id`) REFERENCES `par_rab_bagian` (`bagian_id`);

--
-- Ketidakleluasaan untuk tabel `par_sub_komponen`
--
ALTER TABLE `par_sub_komponen`
  ADD CONSTRAINT `par_sub_komponen_ibfk_1` FOREIGN KEY (`komponen_id`) REFERENCES `par_komponen` (`komponen_id`);

--
-- Ketidakleluasaan untuk tabel `par_sub_komponen_2`
--
ALTER TABLE `par_sub_komponen_2`
  ADD CONSTRAINT `par_sub_komponen_2_ibfk_1` FOREIGN KEY (`sub_komponen_id`) REFERENCES `par_sub_komponen` (`sub_komponen_id`);

--
-- Ketidakleluasaan untuk tabel `rab_detail`
--
ALTER TABLE `rab_detail`
  ADD CONSTRAINT `rab_detail_ibfk_1` FOREIGN KEY (`rab_entry_id`) REFERENCES `rab_entry` (`id_rab_entry`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rab_entry`
--
ALTER TABLE `rab_entry`
  ADD CONSTRAINT `rab_entry_ibfk_1` FOREIGN KEY (`ro_id`) REFERENCES `par_ro` (`ro_id`),
  ADD CONSTRAINT `rab_entry_ibfk_2` FOREIGN KEY (`komponen_id`) REFERENCES `par_komponen` (`komponen_id`),
  ADD CONSTRAINT `rab_entry_ibfk_3` FOREIGN KEY (`sub_komponen_id`) REFERENCES `par_sub_komponen` (`sub_komponen_id`),
  ADD CONSTRAINT `rab_entry_ibfk_4` FOREIGN KEY (`sub_komponen_2_id`) REFERENCES `par_sub_komponen_2` (`sub_komponen_2_id`),
  ADD CONSTRAINT `rab_entry_ibfk_5` FOREIGN KEY (`id_akun`) REFERENCES `par_kode_akun` (`id_akun`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`biro_id`) REFERENCES `par_rab_biro` (`biro_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`bagian_id`) REFERENCES `par_rab_bagian` (`bagian_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
