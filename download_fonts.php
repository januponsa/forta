<?php

$fonts = [
    // Titillium Web
    'TitilliumWeb-Regular.ttf' => 'https://fonts.gstatic.com/s/titilliumweb/v19/NaPecZTIAOhVxoMyOr9n_E7fRMQ.ttf',
    'TitilliumWeb-Bold.ttf' => 'https://fonts.gstatic.com/s/titilliumweb/v19/NaPDcZTIAOhVxoMyOr9n_E7ffHjDKIw.ttf',
    'TitilliumWeb-Italic.ttf' => 'https://fonts.gstatic.com/s/titilliumweb/v19/NaPAcZTIAOhVxoMyOr9n_E7fdMbmCA.ttf',
    'TitilliumWeb-BoldItalic.ttf' => 'https://fonts.gstatic.com/s/titilliumweb/v19/NaPFcZTIAOhVxoMyOr9n_E7fdMbetIlzZg.ttf',
    
    // Carlito (as Calibri replacement)
    'Carlito-Regular.ttf' => 'https://fonts.gstatic.com/s/carlito/v4/3Jn9SDPw3m-pk039PDA.ttf',
    'Carlito-Bold.ttf' => 'https://fonts.gstatic.com/s/carlito/v4/3Jn4SDPw3m-pk039BIykaX0.ttf',
    'Carlito-Italic.ttf' => 'https://fonts.gstatic.com/s/carlito/v4/3Jn_SDPw3m-pk039DDKBSQ.ttf',
    'Carlito-BoldItalic.ttf' => 'https://fonts.gstatic.com/s/carlito/v4/3Jn6SDPw3m-pk039DDK59XglVg.ttf',
];

$dir = __DIR__ . '/public/fonts';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

foreach ($fonts as $filename => $url) {
    echo "Downloading $filename...\n";
    $content = file_get_contents($url);
    file_put_contents("$dir/$filename", $content);
}

echo "All fonts downloaded successfully.\n";
