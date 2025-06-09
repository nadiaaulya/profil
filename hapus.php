<?php
require 'koneksi.php';

if (isset($_GET['id_portofolio'])) {
    $id_portofolio = $_GET['id_portofolio'];

    // Cek apakah data dengan ID tersebut ada
    $cek_sql = "SELECT * FROM portofolio WHERE id_portofolio = ?";
    $stmt = mysqli_prepare($koneksi, $cek_sql);
    mysqli_stmt_bind_param($stmt, "s", $id_portofolio);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Lanjutkan hapus
        $delete_sql = "DELETE FROM portofolio WHERE id_portofolio = ?";
        $stmt_delete = mysqli_prepare($koneksi, $delete_sql);
        mysqli_stmt_bind_param($stmt_delete, "s", $id_portofolio);

        if (mysqli_stmt_execute($stmt_delete)) {
            if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                header("Location: index.php?status=sukses&pesan=" . urlencode("Data berhasil dihapus."));
                exit();
            } else {
                header("Location: index.php?status=gagal&pesan=" . urlencode("Tidak ada data yang dihapus."));
                exit();
            }
        } else {
            header("Location: index.php?status=gagal&pesan=" . urlencode("Gagal menghapus data: " . mysqli_stmt_error($stmt_delete)));
            exit();
        }
    } else {
        header("Location: index.php?status=gagal&pesan=" . urlencode("Data tidak ditemukan."));
        exit();
    }
} else {
    header("Location: index.php?status=gagal&pesan=" . urlencode("Parameter ID tidak ditemukan."));
    exit();
}
?>
