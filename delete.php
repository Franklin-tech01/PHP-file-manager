<?php
$target = __DIR__ . '/' . $_POST['itemToDelete'];

if (file_exists($target)) {
    if (is_file($target)) {
        unlink($target);
    } elseif (is_dir($target)) {
        // Use a recursive function to delete a folder and its contents
        function rrmdir($dir) {
            foreach (glob($dir . '/*') as $file) {
                if (is_dir($file)) {
                    rrmdir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dir);
        }
        rrmdir($target);
    }
    echo "Item deleted successfully.";
} else {
    echo "Item not found.";
}
?>
