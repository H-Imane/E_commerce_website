<?php
// return json responses
header('Content-Type: application/json; charset=utf-8');

// start / load session
require "../../session/session.php";

// remove all session variables
session_unset();

// destroy the session completely
session_destroy();

// confirm logout success
echo json_encode(["success" => true]);
