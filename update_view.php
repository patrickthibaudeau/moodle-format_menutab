<?php

require_once('../../../config.php');

$course = required_param('course', PARAM_INT);
$table_contents = optional_param('table_contents', 0, PARAM_INT);

if (!isset($_SESSION['format_mentuab_view_' . $course])) {
    $_SESSION['format_mentuab_view_' . $course] = $table_contents;
} else {
    $_SESSION['format_mentuab_view_' . $course] = $table_contents;
}

print_object('Session = ' . $_SESSION['format_mentuab_view_' . $course]);