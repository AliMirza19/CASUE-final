<?php
// Download Sample CSV Template
// Roman Urdu comments ke saath
session_start();

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// CSV content banao
$csv_content = "Registration ID,Name,Email\n";
$csv_content .= "BSE123456,Ali Hassan,ali.hassan@cause.edu.pk\n";
$csv_content .= "BSE234567,Sara Ahmed,sara.ahmed@cause.edu.pk\n";
$csv_content .= "BSE345678,Muhammad Usman,m.usman@cause.edu.pk\n";
$csv_content .= "BSE456789,Fatima Khan,fatima.khan@cause.edu.pk\n";
$csv_content .= "BSE567890,Ahmed Ali,ahmed.ali@cause.edu.pk\n";
$csv_content .= "BFE123456,Dr. Muhammad Khan,dr.khan@cause.edu.pk\n";
$csv_content .= "BFE234567,Prof. Ayesha Ahmed,prof.ayesha@cause.edu.pk\n";

// Headers set karo for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sample_users_template.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output CSV
echo $csv_content;
exit();
?>
