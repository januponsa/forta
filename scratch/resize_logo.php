<?php
// Resize logo to reasonable size for PDF (max 400px width)
$src = 'C:/Users/userJ/Documents/fortain/storage/app/public/document-assets/logos/I3Q8PeUFyiemnW2KabptW8vZnXZalKcazGpfxlpI.png';
$dst = 'C:/Users/userJ/Documents/fortain/storage/app/public/document-assets/logos/logo_pdf_optimized.png';

$info = getimagesize($src);
$w = $info[0];
$h = $info[1];

$newW = 400;
$newH = (int)($h * ($newW / $w));

$srcImg = imagecreatefrompng($src);
$dstImg = imagecreatetruecolor($newW, $newH);

// Preserve transparency
imagealphablending($dstImg, false);
imagesavealpha($dstImg, true);
$transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
imagefill($dstImg, 0, 0, $transparent);

imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $w, $h);
imagepng($dstImg, $dst, 9); // max compression

imagedestroy($srcImg);
imagedestroy($dstImg);

echo "Original: {$w}x{$h}, " . filesize($src) . " bytes\n";
echo "Resized: {$newW}x{$newH}, " . filesize($dst) . " bytes\n";
echo "Saved to: $dst\n";
