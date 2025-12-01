<?php
require_once __DIR__.'/auth.php';
function requireLogin(){ if(!isLoggedIn()){ header('Location: /user/login.php'); exit; } }
function requireRole($role){
  requireLogin();
  $u = user(); if(!$u || strtolower($u['role'])!==strtolower($role)){ http_response_code(403); echo 'Forbidden'; exit; }
}
