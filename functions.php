<?php
require_once 'config.php';

function is_logged_in(){ return isset($_SESSION['user_id']); }
function is_admin(){ return isset($_SESSION['admin_id']); }
function is_company(){ return isset($_SESSION['company_id']); }

// basic sanitize
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
