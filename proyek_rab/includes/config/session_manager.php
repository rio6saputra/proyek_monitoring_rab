<?php
// includes/config/session_manager.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>