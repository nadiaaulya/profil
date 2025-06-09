<?php
// edit.php
include 'koneksi.php';

$error = '';
$success = '';
$id_portofolio_edit = '';
$nama_kegiatan_edit = '';
$waktu_kegiatan_edit = '';

// Ambil data kegiatan berdasarkan ID jika ada parameter GET 'id'
if (isset($_GET['id'])) {
    $id_portofolio_edit = mysqli_real_escape_string($koneksi, $_GET['id']);
    $query_select = "SELECT id_portofolio, nama_kegiatan, waktu_kegiatan FROM portofolio WHERE id_portofolio = ?";
    $stmt_select = mysqli_prepare($koneksi, $query_select);
    
    if ($stmt_select) {
        mysqli_stmt_bind_param($stmt_select, "s", $id_portofolio_edit);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        
        if (mysqli_num_rows($result_select) == 1) {
            $data = mysqli_fetch_assoc($result_select);
            $nama_kegiatan_edit = $data['nama_kegiatan'];
            $waktu_kegiatan_edit = $data['waktu_kegiatan'];
        } else {
            $error = "Data tidak ditemukan.";
        }
        mysqli_stmt_close($stmt_select);
    } else {
        $error = "Error menyiapkan pernyataan select: " . mysqli_error($koneksi);
    }
}

// Tangani pengiriman form edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_portofolio_old = mysqli_real_escape_string($koneksi, trim($_POST['id_portofolio_old'])); // ID lama (untuk WHERE clause)
    $id_portofolio_new = mysqli_real_escape_string($koneksi, trim($_POST['id_portofolio'])); // ID baru
    $nama_kegiatan = mysqli_real_escape_string($koneksi, trim($_POST['nama_kegiatan']));
    $waktu_kegiatan = mysqli_real_escape_string($koneksi, trim($_POST['waktu_kegiatan']));

    if (empty($id_portofolio_new) || empty($nama_kegiatan) || empty($waktu_kegiatan)) {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah ID baru sudah ada dan berbeda dari ID lama
        $id_exists = false;
        if ($id_portofolio_new !== $id_portofolio_old) {
            $cek_query = "SELECT id_portofolio FROM portofolio WHERE id_portofolio = ?";
            $stmt_cek = mysqli_prepare($koneksi, $cek_query);
            if ($stmt_cek) {
                mysqli_stmt_bind_param($stmt_cek, "s", $id_portofolio_new);
                mysqli_stmt_execute($stmt_cek);
                $result_cek = mysqli_stmt_get_result($stmt_cek);
                if (mysqli_num_rows($result_cek) > 0) {
                    $id_exists = true;
                    $error = "ID '$id_portofolio_new' sudah terdaftar! Silakan gunakan ID yang berbeda.";
                }
                mysqli_stmt_close($stmt_cek);
            } else {
                $error = "Error menyiapkan pernyataan cek ID: " . mysqli_error($koneksi);
            }
        }

        if (!$id_exists) {
            // Update data
            $update_query = "UPDATE portofolio SET id_portofolio = ?, nama_kegiatan = ?, waktu_kegiatan = ? WHERE id_portofolio = ?";
            $stmt_update = mysqli_prepare($koneksi, $update_query);

            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "ssss", $id_portofolio_new, $nama_kegiatan, $waktu_kegiatan, $id_portofolio_old);
                if (mysqli_stmt_execute($stmt_update)) {
                    mysqli_stmt_close($stmt_update);
                    mysqli_close($koneksi); // Tutup koneksi sebelum redirect
                    header("Location: index.php?status=sukses&pesan=" . urlencode("Data berhasil diupdate"));
                    exit();
                } else {
                    $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt_update);
            } else {
                $error = "Error menyiapkan pernyataan update: " . mysqli_error($koneksi);
            }
        }
    }
    // Perbarui nilai untuk form jika ada error setelah submit
    $id_portofolio_edit = $id_portofolio_new;
    $nama_kegiatan_edit = $nama_kegiatan;
    $waktu_kegiatan_edit = $waktu_kegiatan;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kegiatan</title>
    <style>
        /* CSS styling untuk form */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f3ef; /* Light cream background */
            color: #4e342e; /* Dark brown text */
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #fff8f3; /* Lighter cream container */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(107, 76, 59, 0.15); /* Brownish shadow */
            border: 1px solid #e7d6cb; /* Light brown border */
            /* Removed backdrop-filter as it makes less sense with solid background */
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            color: #6b4c3b; /* Medium brown heading */
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #a9745f; /* Lighter brown subtitle */
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333; /* Darker text for labels */
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e7d6cb; /* Light brown border for inputs */
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #fbf5f0; /* Very light cream input background */
            color: #4e342e; /* Dark brown text for inputs */
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #6b4c3b; /* Medium brown on focus */
            background-color: white;
            box-shadow: 0 0 0 3px rgba(107, 76, 59, 0.2); /* Brownish shadow on focus */
            transform: translateY(-2px);
        }
        
        textarea {
            resize: vertical;
            height: 120px;
            font-family: inherit;
            line-height: 1.5;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 35px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            white-space: nowrap; /* Prevent text wrapping inside button */
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6b4c3b 0%, #5a3a2d 100%); /* Brown gradient */
            color: white;
            box-shadow: 0 4px 15px rgba(107, 76, 59, 0.4); /* Brown shadow */
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 76, 59, 0.5); /* Stronger brown shadow */
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #a9745f 0%, #926451 100%); /* Lighter brown gradient */
            color: white;
            box-shadow: 0 4px 15px rgba(169, 116, 95, 0.4); /* Lighter brown shadow */
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(169, 116, 95, 0.5); /* Stronger lighter brown shadow */
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-error {
            background-color: #ffe0e0; /* Light red */
            border: 1px solid #ffb3b3; /* Red border */
            color: #b30000; /* Darker red text */
        }
        
        .alert-success {
            background-color: #e0ffe0; /* Light green */
            border: 1px solid #b3ffb3; /* Green border */
            color: #008000; /* Darker green text */
        }
        
        .alert-info {
            background-color: #f0e6d2; /* Very light brown/tan for info */
            border: 1px solid #d2c4b4; /* Muted brown border for info */
            color: #6b4c3b; /* Medium brown text for info */
        }
        
        .required {
            color: #b30000; /* Dark red for required asterisk */
        }
        
        .form-info {
            background-color: #f3e5db; /* Light brown background */
            border: 1px solid #d4c1ad; /* Slightly darker brown border */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            color: #5a3a2d; /* Dark brown text */
        }
        
        .char-count {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .input-help {
            font-size: 12px;
            color: #888; /* Slightly lighter gray for help text */
            margin-top: 5px;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 25px;
            }
            
            .header h2 {
                font-size: 24px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Loading animation */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✏️ Edit Data Kegiatan</h2>
            <p class="subtitle">Ubah informasi kegiatan portofolio</p>
        </div>
        
        <div class="form-info alert-info">
            <div>
                <strong>ℹ️ Informasi Penting:</strong><br>
                • Pastikan ID unik (jika diubah) dan tidak sama dengan data lain<br>
                • Semua field yang bertanda (*) wajib diisi<br>
                • Perubahan akan disimpan setelah validasi berhasil
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
        
        <form method="POST" action="" id="editForm">
            <input type="hidden" name="id_portofolio_old" value="<?php echo htmlspecialchars($id_portofolio_edit); ?>">
            
            <div class="form-group">
                <label for="id_portofolio">🆔 ID Kegiatan <span class="required">*</span></label>
                <input type="text" 
                       id="id_portofolio" 
                       name="id_portofolio" 
                       value="<?php echo htmlspecialchars($id_portofolio_edit); ?>" 
                       required
                       placeholder="Contoh: KGT001"
                       maxlength="20"
                       pattern="[A-Za-z0-9_-]+"
                       title="ID hanya boleh mengandung huruf, angka, underscore, dan dash">
                <div class="input-help">
                    ID harus unik. Jika diubah, pastikan tidak konflik dengan ID lain (max 20 karakter)
                </div>
            </div>
            
            <div class="form-group">
                <label for="nama_kegiatan">📝 Nama Kegiatan <span class="required">*</span></label>
                <input type="text" 
                       id="nama_kegiatan" 
                       name="nama_kegiatan" 
                       value="<?php echo htmlspecialchars($nama_kegiatan_edit); ?>" 
                       required
                       placeholder="Contoh: Seminar informtic engineering"
                       maxlength="255">
                <div class="input-help">
                    Nama lengkap kegiatan (max 255 karakter)
                </div>
            </div>
            
            <div class="form-group">
                <label for="waktu_kegiatan">⏰ Waktu Kegiatan <span class="required">*</span></label>
                <textarea id="waktu_kegiatan" 
                          name="waktu_kegiatan" 
                          required
                          placeholder="Contoh:&#10;Senin, 15 Januari 2024"
                          maxlength="500"><?php echo htmlspecialchars($waktu_kegiatan_edit); ?></textarea>
                <div class="char-count">
                    <span id="charCount">0</span>/500 karakter
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    💾 Simpan Perubahan
                </button>
                <a href="index.php" class="btn btn-secondary">
                    ↩️ Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

    <script>
        // Auto focus on first field
        document.getElementById('id_portofolio').focus();
        
        // Character count for textarea
        const textarea = document.getElementById('waktu_kegiatan');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            const count = textarea.value.length;
            charCount.textContent = count;
            
            if (count > 450) {
                charCount.style.color = '#dc3545';
                charCount.style.fontWeight = 'bold';
            } else if (count > 400) {
                charCount.style.color = '#ffc107';
                charCount.style.fontWeight = '600';
            } else {
                charCount.style.color = '#28a745';
                charCount.style.fontWeight = '500';
            }
        }
        
        textarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
        
        // Form validation and submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const id = document.getElementById('id_portofolio').value.trim();
            const nama_kegiatan = document.getElementById('nama_kegiatan').value.trim();
            const waktu_kegiatan = document.getElementById('waktu_kegiatan').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            
            // Validation
            if (!id || !nama_kegiatan || !waktu_kegiatan) {
                e.preventDefault();
                alert('⚠️ Semua field wajib diisi!');
                return false;
            }
            
            // ID format validation
            const idPattern = /^[A-Za-z0-9_-]+$/;
            if (!idPattern.test(id)) {
                e.preventDefault();
                alert('⚠️ ID hanya boleh mengandung huruf, angka, underscore (_), dan dash (-)!');
                document.getElementById('id_portofolio').focus();
                return false;
            }
            
            // Confirmation
            if (!confirm(`✅ Konfirmasi Perubahan Data\n\nID Baru: ${id}\nKegiatan: ${nama_kegiatan.substring(0, 50)}${nama_kegiatan.length > 50 ? '...' : ''}\n\nApakah Anda yakin ingin menyimpan perubahan ini?`)) {
                e.preventDefault();
                return false;
            }
            
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '⏳ Menyimpan...';
            submitBtn.disabled = true;
        });
        
        // Auto-trim whitespace on blur
        document.querySelectorAll('input[type="text"], textarea').forEach(function(element) {
            element.addEventListener('blur', function() {
                this.value = this.value.trim();
                if (this.id === 'waktu_kegiatan') {
                    updateCharCount();
                }
            });
        });
        
        // Input formatting for ID
        document.getElementById('id_portofolio').addEventListener('input', function() {
            // Remove invalid characters as user types
            this.value = this.value.replace(/[^A-Za-z0-9_-]/g, '');
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (!alert.classList.contains('alert-info')) {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.style.display = 'none', 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>
