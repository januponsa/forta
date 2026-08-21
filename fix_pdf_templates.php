<?php
$files = glob('resources/views/pdf/defense/*.blade.php');
foreach($files as $file) {
    $content = file_get_contents($file);
    $content = str_replace("?? '-'", "?? ''", $content);
    $content = str_replace("?? '- '", "?? ''", $content);
    $content = str_replace("= '-'", "= ''", $content);
    $content = str_replace("'N/A'", "''", $content);
    
    // Fix specific F1 decision
    $content = str_replace("\$keputusan = '';", "\$keputusan = 'Belum Diputuskan';", $content);
    
    file_put_contents($file, $content);
}
echo 'Replaced placeholders.';
