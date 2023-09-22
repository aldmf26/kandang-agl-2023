<?php
function getLargestDirectory($rootDir) {
    $dirs = scandir($rootDir);
    $largestDir = '';
    $largestSize = 0;

    foreach ($dirs as $dir) {
        if ($dir !== '.' && $dir !== '..') {
            $dirPath = $rootDir . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($dirPath)) {
                $dirSize = getDirectorySize($dirPath);

                if ($dirSize > $largestSize) {
                    $largestSize = $dirSize;
                    $largestDir = $dirPath;
                }
            }
        }
    }

    return $largestDir;
}

function getDirectorySize($dir) {
    $totalSize = 0;
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $totalSize += getDirectorySize($filePath);
            } else {
                $totalSize += filesize($filePath);
            }
        }
    }

    return $totalSize;
}

// Mendapatkan direktori tempat file PHP berada
$currentDirectory = __DIR__;

$largestDirectory = getLargestDirectory($currentDirectory);

if (!empty($largestDirectory)) {
    echo "Direktori terbesar adalah: $largestDirectory dengan ukuran " . number_format(getDirectorySize($largestDirectory)) . " bytes.";
} else {
    echo "Tidak ada direktori yang ditemukan di $currentDirectory.";
}
?>
