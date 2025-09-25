<div id="session-warning-modal" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px; text-align: center;">
        <h3 id="session-modal-title">Sesi Akan Berakhir</h3>
        <hr>
        <div id="session-modal-body">
            <p>Sesi Anda akan berakhir dalam <strong id="session-countdown">60</strong> detik karena tidak ada aktivitas.</p>
            <p>Apakah Anda ingin tetap login?</p>
        </div>
        <hr>
        <div style="display: flex; justify-content: center; gap: 15px;">
            <button id="logout-session-btn" class="btn-secondary" style="background-color: #dc3545;">Logout</button>
            <button id="stay-logged-in-btn" class="btn-primary">Ya, Tetap Login</button>
        </div>
    </div>
</div>
<script type="module" src="js/main.js"></script>
<script type="module" src="js/session_timer.js"></script>

</body>
</html>
<?php  
if (isset($koneksi)) {
    mysqli_close($koneksi);
} 
?>