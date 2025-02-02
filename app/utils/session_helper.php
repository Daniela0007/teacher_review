<?php

if (!isset($_SESSION)) {
    session_start();
}

function isAuthenticated() {
    return isset($_SESSION['username']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        header("Location: ?controller=Login&action=index");
        exit;
    }
}

