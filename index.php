<?php
include 'koneksi.php';

// Ambil data dari database
$query = "SELECT * FROM portofolio";
$result = mysqli_query($koneksi, $query);

$no = 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nadia</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#tentang">Tentang Saya</a></li>
                <li><a href="#portofolio">Portofolio</a></li>
                <li><a href="#opini">Opini</a></li>
                <li><a href="#hubungi">Hubungi Saya</a></li>
                <li class="dropdown">
                    <a href="#">Lainnya</a>
                    <div class="dropdown-content">
                        <a href="https://www.instagram.com/n.nadiaaulya/" target="_blank">Instagram</a>
                        <a href="https://www.tiktok.com/@herewith.na/" target="_blank">Tiktok</a>
                        <a href="https://www.facebook.com/" target="_blank">facebook</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <section class="Beranda" id="beranda">
        <img src="foto1.jpg" alt="Foto Saya" width="300">
        <div class="beranda">
            <h1>Nadia Aulya Oktaviana</h1>
            <p>Saya adalah seorang mahasiswa Universitas Nahdlatul Ulama Sunan Giri Bojonegoro</p>
        </div>
    </section>

    <section class="Tentang Saya" id="tentang">
        <h1>Tentang Saya</h1>
        <div class="tentang-konten">
            <div class="tentang saya">
                <p>Halo,</p>
                <p>Saya lahir di Tuban 11 juli 2005 dan saat ini berusia 19 tahun. Saya tinggal di Desa Selogabus, Kecamatan Parengan, Kabupaten Tuban.</p>
                <p>Saya memiliki minat yang besar dalam bidang teknologi dan data, serta bercita-cita menjadi seorang Data Analyst profesional yang mampu memberikan insight strategis melalui proses pengolahan dan analisis data.</p>
                <p>Mengejar mimpi itu seperti mendaki gunung, mungkin melelahkan, ada kalanya terjal, tapi pemandangan di puncaknya akan sepadan dengan setiap langkah yang kita ambil. Terus melangkah, jangan pernah menyerah!</p>
            </div>
            <div>
                <img src="foto.na1.jpg" alt="foto tentang saya" width="200">
            </div>
        </div>
    </section>

    <section class="portofolio" id="portofolio">

  <h1>Portofolio</h1>

  <!-- Toggle Button ke halaman tambah -->
  <div style="margin-bottom: 20px;">
    <a href="tambah.php">
      <button type="button">➕ Tambah Portofolio</button>
    </a>
  </div>

  <table>
    <thead>
      <tr>
        <th class="nomor">No</th>
        <th class="nomor">Nama Kegiatan</th>
        <th class="nomor">Waktu Kegiatan</th>
        <th class="nomor">Aksi</th> <!-- Tambahan kolom aksi -->
      </tr>
    </thead>
    <tbody>
      <?php
      $koneksi = new mysqli("localhost", "root", "", "portofolio");
      if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
      }

      $sql = "SELECT * FROM portofolio";
      $result = $koneksi->query($sql);
      $no = 1;

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td class='nomor'>" . $no++ . "</td>";
          echo "<td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>";
          echo "<td>" . htmlspecialchars($row['waktu_kegiatan']) . "</td>";
          echo "<td>
                  <a href='edit.php?id_portofolio=" . $row['id_portofolio'] . "'>✏️ Edit</a> |
                  <a href='hapus.php?id_portofolio=" . $row['id_portofolio'] . "' onclick=\"return confirm('Yakin ingin menghapus?')\">🗑️ Hapus</a>
                </td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
      }

      $koneksi->close();
      ?>
    </tbody>
  </table>

</section>


    <section class="opini" id="opini">
        <h2>Opini</h2>
        <div class="grid-opini">
            <div class="item-opini">
                <img src="opsi1.png" alt="Opini 1">
                <a href="https://nadiaaulyaaa.blogspot.com/?m=1" target="_blank">
                    <p>Blog pribadi</p>
                </a>
            </div>
            <div class="item-opini">
                <img src="opini2.jpg" alt="Opini 2">
                <a href="https://docs.google.com/document/d/1I4Jp-uLifXJYA4Cob3gFFkvtAj0aMeqg/edit?usp=drivesdk&ouid=112181713961432928413&rtpof=true&sd=true" target="_blank">
                    <p>Laporan Proyek CSS</p>
                </a>
            </div>
            <div class="item-opini">
                <img src="opini3.png" alt="Opini 3">
                <a href="https://unugiri.ac.id/" target="_blank">
                    <p>Universitas Nahdlatul Ulama Sunan Giri</p>
                </a>
            </div>
            <div class="item-opini">
                <img src="opini4.png" alt="Opini 4">
                <a href="https://www.w3schools.com/" target="_blank">
                    <p>Situs Pembelajaran Coding</p>
                </a>
            </div>
            <div class="item-opini">
                <img src="opini5.png" alt="Opini 5">
                <a href="https://open.spotify.com/playlist/0KgtAyusO5t3HmUmECYJmh?si=f5b548527fac48d7" target="_blank">
                    <p>Song of The Day</p>
                </a>
            </div>
            <div class="item-opini">
                <img src="opini6.png" alt="Opini 6">
                <a href="https://id.wikipedia.org/wiki/Pemrograman" target="_blank">
                    <p>Pemrograman?</p>
                </a>
            </div>
        </div>
    </section>
    
    <section class="kontak" id="hubungi">
        <h2>Hubungi Saya</h2>
        <div class="kontak-container">
            <div class="form-kontak">
                <form action="process_form.php" method="POST"> <input type="email" name="email" placeholder="Gmail" required>
                    <input type="text" name="name" placeholder="Nama" required>
                    <input type="text" name="subject" placeholder="Subjek" required>
                    <textarea name="message" placeholder="Pesan" rows="5" required></textarea>
                    <button type="submit">Kirim</button>
                </form>
            </div>
            <div class="map-kontak">
                <h3 style="color: #eaeaea;">Lokasi</h3>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3402.270649617751!2d111.85843787424133!3d-7.125317892878473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7782769e17d561%3A0xb340ad24e2f73993!2sMas%20yudi%20mbedrek!5e1!3m2!1sid!2sid!4v1749225017558!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
    
    <footer>
        <p>© by: Nadia Aulya Oktaviana</p>
        <?php
        // Display the current year dynamically
        echo "<p>Tahun saat ini: " . date("Y") . "</p>";
        ?>
    </footer>
    
</body>
</html>
       
