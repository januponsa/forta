<?php
$f = 'C:/Users/userJ/Documents/fortain/storage/app/public/document-assets/logos/I3Q8PeUFyiemnW2KabptW8vZnXZalKcazGpfxlpI.png';
echo "Size: " . filesize($f) . " bytes\n";
$i = getimagesize($f);
echo "Dimensions: " . $i[0] . "x" . $i[1] . "\n";
echo "Type: " . image_type_to_mime_type($i[2]) . "\n";
