<?php include('inc/head.php'); ?>

<?php
    // L’application permet de :
    // Lister dynamiquement les répertoires et fichiers contenus dans /files,
    $dir = opendir('files');

    function showEntry(string $path)
    {
        $handle = opendir($path);
        while ($entry = readdir($handle)) {
            if (!in_array($entry, array('.','..'))) {
                if (is_dir($path . '/' . $entry)) {
                    showEntry($path . '/' . $entry);
                }
                echo '<div><a href="?f=' . $path . '/' . $entry . '">' . $entry . '</a> <a href="?d=' . $path . '/' . $entry . '" style="color:red; display:inlineBlock; marginLeft:10px"><button>x</button></a></div> ';
            }
        }
    }

    function getContentFile(){
        if (isset($_GET["f"])) {
            $file = $_GET["f"];
            if ((strstr($_GET["f"], ".html") || strstr($_GET["f"], ".txt"))) {
                $content = file_get_contents($file);
                return $content;
            }
        }
    }

    function getNameFile(){
        if (isset($_GET["f"])) {
            $file = $_GET["f"];
            return "<h1>" . $file . "</h1>";
        }
    }

    function getImg()
    {
        
        if (isset($_GET["f"]) && strstr($_GET["f"], ".jpg")) {
            $file = $_GET["f"];
            return '<img src="' . $file . '" style="width:200px; margin:2rem 0">';
        }
    }

    // Supprimer un fichier ou un répertoire,

    function deleteEntry($path)
    {
        if (!is_dir($path) && !is_file($path))  {
            header('Location: index.php');
        }
        if (is_dir($path)) {
            $handle = opendir($path);
            while ($entry = readdir($handle)) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($path . '/' . $entry)) {
                        deleteEntry($path . '/' . $entry);
                    }
                    if (is_file($path . '/' . $entry)) {
                        echo $path . '/' .  $entry . " was deleted <br>";
                        unlink($path . '/' .  $entry);
                    }
                }
            }
            echo $path . " was deleted <br>";
            rmdir($path);
        }
        if (is_file($path)) {
            echo $path . " was deleted <br>";
            unlink($path);
        }
    }
    if (isset($_GET["d"])) {
        $entry = $_GET["d"];
        deleteEntry($entry);
    }
    
    // Éditer un fichier texte (.txt/.html),

    if (isset($_POST["file"])) {
        $file = $_POST["file"];
        $file = fopen($file, "w");
        fwrite($file, stripcslashes($_POST["file-content"]));
        fclose($file);
    }


    showEntry('files');
    echo getNameFile();
    echo getImg();
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <div>
        <textarea name="file-content" id="file-content" cols="100" rows="20">
        <?php echo getContentFile(); ?>
        </textarea>
    </div>
    <div>
        <input type="hidden" name="file" value="<?php if (isset($_GET["f"])) echo $_GET["f"];?>">
        <input type="submit" value="Submit">
    </div>
</form>

<?php include('inc/foot.php'); ?>