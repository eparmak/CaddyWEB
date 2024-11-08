<?php
function compressFolderToGz($folderPath, $outputGzPath) {
    // Create a temporary tar file path
    $tarPath = $outputGzPath . '.tar';

    // Check if the compressed file already exists and delete it
    if (file_exists($outputGzPath . '.tar.gz')) {
        unlink($outputGzPath . '.tar.gz');
    }

    try {
        // Create a PharData object to handle the tar file
        $phar = new PharData($tarPath);

        // Add the folder to the tar archive
        $phar->buildFromDirectory($folderPath);

        // Compress the tar archive with gzip
        $phar->compress(Phar::GZ);

        // Remove the temporary tar file, leaving only the .tar.gz file
        unlink($tarPath);
    } catch (Exception $e) {
        // Clean up and throw the exception if something goes wrong
        if (file_exists($tarPath)) {
            unlink($tarPath);
        }
        throw $e;
    }

    // Return the path to the .tar.gz file
    return $outputGzPath . '.tar.gz';
}
?>