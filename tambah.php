<?php
// tambah.php
include 'koneksi.php'; // Hubungkan ke database

$error = '';
$success = '';

// Proses form jika ada data POST yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan bersihkan (sanitize) data dari form
    $id_portofolio = mysqli_real_escape_string($koneksi, trim($_POST['id_portofolio']));
    $nama_kegiatan = mysqli_real_escape_string($koneksi, trim($_POST['nama_kegiatan'])); 
    $waktu_kegiatan = mysqli_real_escape_string($koneksi, trim($_POST['waktu_kegiatan']));
    
    // Validasi input: pastikan semua field tidak kosong
    if (empty($id_portofolio) || empty($nama_kegiatan) || empty($waktu_kegiatan)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Cek apakah ID sudah ada di database untuk mencegah duplikasi
        $cek_query = "SELECT id_portofolio FROM portofolio WHERE id_portofolio = ?";
        $stmt_cek = mysqli_prepare($koneksi, $cek_query);
        
        if ($stmt_cek) {
            mysqli_stmt_bind_param($stmt_cek, "s", $id_portofolio);
            mysqli_stmt_execute($stmt_cek);
            $result_cek = mysqli_stmt_get_result($stmt_cek);
            
            if (mysqli_num_rows($result_cek) > 0) {
                // Jika ID sudah ada, tampilkan pesan error
                $error = "ID kegiatan '$id_portofolio' sudah terdaftar! Silakan gunakan ID yang berbeda.";
            } else {
                // Jika ID unik, masukkan data baru ke database
                $insert_query = "INSERT INTO portofolio (id_portofolio, nama_kegiatan, waktu_kegiatan) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($koneksi, $insert_query);
                
                if ($insert_stmt) {
                    mysqli_stmt_bind_param($insert_stmt, "sss", $id_portofolio, $nama_kegiatan, $waktu_kegiatan);
                    
                    if (mysqli_stmt_execute($insert_stmt)) {
                        // Jika berhasil disimpan, arahkan kembali ke index.php dengan pesan sukses
                        mysqli_stmt_close($insert_stmt);
                        mysqli_stmt_close($stmt_cek);
                        mysqli_close($koneksi); // Penting: tutup koneksi sebelum redirect
                        header("Location: index.php?status=sukses&pesan=" . urlencode("Data kegiatan berhasil ditambahkan."));
                        exit();
                    } else {
                        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
                    }
                    mysqli_stmt_close($insert_stmt);
                } else {
                    $error = "Error saat menyiapkan pernyataan insert: " . mysqli_error($koneksi);
                }
            }
            mysqli_stmt_close($stmt_cek);
        } else {
            $error = "Error saat menyiapkan pernyataan cek ID: " . mysqli_error($koneksi);
        }
    }
}

// Pertahankan nilai form jika ada error setelah submit, agar user tidak perlu mengisi ulang
$form_id = isset($_POST['id_portofolio']) ? htmlspecialchars($_POST['id_portofolio']) : '';
$form_nama_kegiatan = isset($_POST['nama_kegiatan']) ? htmlspecialchars($_POST['nama_kegiatan']) : '';
$form_waktu_kegiatan = isset($_POST['waktu_kegiatan']) ? htmlspecialchars($_POST['waktu_kegiatan']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Kegiatan</title>
    <link rel="icon" href="https://via.placeholder.com/16x16?text=➕" type="image/x-icon">
    <style>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f3ef;
        color: #4e342e;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 700px;
        margin: 30px auto;
        background-color: #fff8f3;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(107, 76, 59, 0.1);
        border: 1px solid #e7d6cb;
    }

    .header h2 {
        color: #6b4c3b;
        margin-top: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header h2 svg {
        fill: #6b4c3b;
        width: 24px;
        height: 24px;
    }

    .subtitle {
        font-size: 0.95em;
        color: #a9745f;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #5d4037;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #d8b4a0;
        border-radius: 6px;
        font-size: 1em;
        background-color: #fffaf7;
        color: #4e342e;
        box-sizing: border-box;
    }

    input:focus,
    textarea:focus {
        border-color: #a9745f;
        outline: none;
        box-shadow: 0 0 4px #a9745f40;
    }

    textarea {
        resize: vertical;
        min-height: 120px;
    }

    .btn-group {
        display: flex;
        justify-content: flex-start;
        gap: 12px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 1em;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.2s ease;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #6b4c3b;
        color: #fff8f3;
    }

    .btn-primary:hover {
        background-color: #5a3a2d;
    }

    .btn-secondary {
        background-color: #d8b4a0;
        color: #4e342e;
    }

    .btn-secondary:hover {
        background-color: #cba28e;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 0.95em;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .alert-info {
        background-color: #fbe9e0;
        color: #5d4037;
        border: 1px solid #e0bfa5;
    }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Tambah Data Kegiatan
            </h2>
            <p class="subtitle">Tambahkan kegiatan baru ke dalam database</p>
        </div>
        
        <div class="alert alert-info">
            <span>💡</span>
            <div>
                <strong>Informasi Penting:</strong>
                <ul>
                    <li>Pastikan ID unik dan tidak sama dengan data yang sudah ada.</li>
                    <li>Semua field yang bertanda (*) wajib diisi.</li>
                    <li>Data akan disimpan ke database setelah validasi berhasil.</li>
                </ul>
            </div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span>❌</span>
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <strong>Sukses:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="tambahForm">
            <div class="form-group">
                <label for="id_portofolio">
                    <span class="input-icon">🆔</span> ID Kegiatan <span class="required">*</span>
                </label>
                <input type="text" 
                       id="id_portofolio" 
                       name="id_portofolio" 
                       value="<?php echo $form_id; ?>" 
                       required
                       placeholder="Contoh: KGT001"
                       maxlength="20"
                       pattern="[A-Za-z0-9_-]+"
                       title="ID hanya boleh mengandung huruf, angka, underscore, dan dash">
                <div class="input-help">
                    ID harus unik (maks. 20 karakter).
                </div>
            </div>
            
            <div class="form-group">
                <label for="nama_kegiatan">
                    <span class="input-icon">📝</span> Nama Kegiatan <span class="required">*</span>
                </label>
                <input type="text" 
                       id="nama_kegiatan" 
                       name="nama_kegiatan" 
                       value="<?php echo $form_nama_kegiatan; ?>" 
                       required
                       placeholder="Contoh: Seminar informatic engineering"
                       maxlength="255">
                <div class="input-help">
                    Nama lengkap kegiatan (maks. 255 karakter).
                </div>
            </div>
            
            <div class="form-group">
                <label for="waktu_kegiatan">
                    <span class="input-icon" style="top: 25px;">⏰</span> Waktu Kegiatan <span class="required">*</span>
                </label>
                <textarea id="waktu_kegiatan" 
                          name="waktu_kegiatan" 
                          required
                          placeholder="Contoh:&#10;Senin, 15 Januari 2024"
                          maxlength="500"><?php echo $form_waktu_kegiatan; ?></textarea>
                <div class="char-count">
                    <span id="charCount">0</span>/500 karakter
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Simpan Data
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
        // Auto focus pada field pertama
        document.getElementById('id_portofolio').focus();
        
        // Fungsi untuk mengupdate jumlah karakter pada textarea
        const textarea = document.getElementById('waktu_kegiatan');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            const count = textarea.value.length;
            charCount.textContent = count;
            
            if (count > 450) {
                charCount.style.color = '#dc3545'; // Merah
                charCount.style.fontWeight = 'bold';
            } else if (count > 400) {
                charCount.style.color = '#ffc107'; // Kuning
                charCount.style.fontWeight = '600';
            } else {
                charCount.style.color = '#28a745'; // Hijau
                charCount.style.fontWeight = '500';
            }
        }
        
        textarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Panggil saat halaman dimuat untuk menampilkan jumlah karakter awal
        
        // Validasi form dan penanganan submit
        document.getElementById('tambahForm').addEventListener('submit', function(e) {
            const id = document.getElementById('id_portofolio').value.trim();
            const nama_kegiatan = document.getElementById('nama_kegiatan').value.trim();
            const waktu_kegiatan = document.getElementById('waktu_kegiatan').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            
            // Validasi sederhana: cek apakah semua field terisi
            if (!id || !nama_kegiatan || !waktu_kegiatan) {
                e.preventDefault(); // Batalkan submit
                alert('⚠️ Semua field wajib diisi!');
                return false;
            }
            
            // Validasi format ID
            const idPattern = /^[A-Za-z0-9_-]+$/;
            if (!idPattern.test(id)) {
                e.preventDefault();
                alert('⚠️ ID hanya boleh mengandung huruf, angka, underscore (_), dan dash (-)!');
                document.getElementById('id_portofolio').focus();
                return false;
            }
            
            // Konfirmasi sebelum submit
            if (!confirm(`✅ Konfirmasi Penyimpanan Data\n\nID: ${id}\nKegiatan: ${nama_kegiatan.substring(0, 50)}${nama_kegiatan.length > 50 ? '...' : ''}\n\nApakah Anda yakin ingin menyimpan data ini?`)) {
                e.preventDefault();
                return false;
            }
            
            // Tambahkan status loading pada tombol setelah submit
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Menyimpan...';
            submitBtn.disabled = true; // Nonaktifkan tombol
        });
        
        // Menghilangkan spasi berlebih pada input saat blur (fokus hilang)
        document.querySelectorAll('input[type="text"], textarea').forEach(function(element) {
            element.addEventListener('blur', function() {
                this.value = this.value.trim();
                if (this.id === 'waktu_kegiatan') {
                    updateCharCount();
                }
            });
        });
        
        // Memastikan ID hanya berisi karakter yang valid
        document.getElementById('id_portofolio').addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z0-9_-]/g, '');
        });
        
        // Sembunyikan alert otomatis setelah 5 detik
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                // Jangan sembunyikan alert-info (Informasi Penting)
                if (!alert.classList.contains('alert-info')) {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.style.display = 'none', 500);
                }
            });
        }, 5000); // 5000 milidetik = 5 detik
    </script>
</body>
</html>
