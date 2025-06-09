<?php
require_once 'includes/functions.php';
if (is_logged_in()) {
    header('Location: homepage.php');
} else {
    header('Location: login.php');
}
exit;
?>