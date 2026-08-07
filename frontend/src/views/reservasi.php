<?php
// Tangkap parameter resto jika dikirim dari halaman sebelumnya (misal: ?page=reservasi&resto=A)
$selected_resto = isset($_GET['resto']) ? htmlspecialchars($_GET['resto']) : '';
?>

<div style="background-color: #f5f0eb; min-height: 100vh; font-family: sans-serif; padding: 40px 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <h2 style="text-align: center; margin-bottom: 30px; font-weight: bold; letter-spacing: 1px;">RESERVASI RESTORAN</h2>
        
        <form action="proses_reservasi.php" method="POST">
            
            <!-- Pilih Restoran -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Pilih Restoran</label>
                <select name="restoran" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px;">
                    <option value="A" <?php if($selected_resto == 'A') echo 'selected'; ?>>Restoran A - Kafiber Cabang Utama</option>
                    <option value="B" <?php if($selected_resto == 'B') echo 'selected'; ?>>Restoran B</option>
                </select>
            </div>

            <!-- Nama Pemesan -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Pemesan</label>
                <input type="text" name="nama_pemesan" value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>" placeholder="NAMA PEMESAN" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Acara -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Acara</label>
                <input type="text" name="acara" placeholder="Birthday, meeting, DLL" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Tanggal & Waktu (Sejajar) -->
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Waktu</label>
                    <input type="time" name="waktu" value="17:00" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <!-- Jumlah Tamu -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jumlah Tamu</label>
                <input type="number" name="jumlah_tamu" placeholder="Masukkan jumlah tamu" style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Catatan -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">CATATAN</label>
                <textarea name="catatan" rows="3" placeholder="Tambahan catatan khusus..." style="width: 100%; padding: 14px; background: #dcdcdc; border: none; border-radius: 10px; font-size: 14px; box-sizing: border-box;"></textarea>
            </div>

            <!-- Pilih Area -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Pilih Area :</label>
                <div style="display: flex; gap: 15px;">
                    <label style="flex: 1; background: #dcdcdc; padding: 30px; text-align: center; border-radius: 10px; cursor: pointer;">
                        <input type="radio" name="area" value="indoor" style="display:none;">
                        <b>GAMBAR INDOR</b>
                    </label>
                    <label style="flex: 1; background: #dcdcdc; padding: 30px; text-align: center; border-radius: 10px; cursor: pointer;">
                        <input type="radio" name="area" value="outdoor" style="display:none;">
                        <b>GAMBAR OUTDOOR</b>
                    </label>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" style="width: 100%; background: #4a3319; color: white; padding: 15px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer;">
                KONFIRMASI RESERVASI
            </button>

        </form>
    </div>
</div>