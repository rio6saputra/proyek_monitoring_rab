// js/session_timer.js
import * as api from './api.js'; // Kita butuh api.js untuk refresh sesi

// Durasi sesi dalam detik (contoh: 30 menit)
// Nilai ini HARUS SAMA dengan session.gc_maxlifetime di php.ini Anda
const SESSION_LIFETIME = 1800; 

// Waktu peringatan sebelum sesi habis (contoh: 2 menit sebelum habis)
const WARNING_TIME = 120; 

let sessionTimeout, warningTimeout, countdownInterval;

const modal = document.getElementById('session-warning-modal');
const countdownDisplay = document.getElementById('session-countdown');
const stayLoggedInBtn = document.getElementById('stay-logged-in-btn');
const logoutBtn = document.getElementById('logout-session-btn');

function startTimers() {
    clearTimeout(warningTimeout);
    clearTimeout(sessionTimeout);
    clearInterval(countdownInterval);

    // Timer untuk menampilkan modal peringatan
    warningTimeout = setTimeout(showWarningModal, (SESSION_LIFETIME - WARNING_TIME) * 1000);

    // Timer untuk logout paksa jika tidak ada respons
    sessionTimeout = setTimeout(() => {
        window.location.href = 'logout.php'; // Alihkan ke halaman logout
    }, SESSION_LIFETIME * 1000);
}

function showWarningModal() {
    if (!modal) return;
    modal.classList.add('visible');
    
    let timeLeft = WARNING_TIME;
    countdownDisplay.textContent = timeLeft;

    countdownInterval = setInterval(() => {
        timeLeft--;
        countdownDisplay.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
}

function hideWarningModal() {
    if (!modal) return;
    modal.classList.remove('visible');
    clearInterval(countdownInterval);
}

function resetSession() {
    hideWarningModal();
    // Panggil API untuk "menyentuh" sesi di server
    api.postApiCall('api/refresh_session.php', {}).then(res => {
        if (res.status === 'success') {
            console.log('Session refreshed');
            startTimers(); // Mulai ulang semua timer
        } else {
            // Jika gagal, mungkin sesi sudah benar-benar habis
            window.location.href = 'logout.php';
        }
    }).catch(() => {
        window.location.href = 'logout.php';
    });
}

// Event Listeners
if (stayLoggedInBtn) {
    stayLoggedInBtn.addEventListener('click', resetSession);
}

if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        window.location.href = 'logout.php';
    });
}

// Reset timer setiap kali ada aktivitas pengguna
['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, startTimers, { once: true });
});

// Mulai timer saat halaman pertama kali dimuat
startTimers();