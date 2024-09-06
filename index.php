<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador de arquivos repetidos</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="file"] {
            margin: 10px 0;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php
    $inputName = "userfiles";
    $inputControlName = "control";
    $hashAlgorithm = "sha256";
    $hashArray = []; // Hashes de arquivos não repetidos.
    $fileNameArray = []; // Nomes de arquivos não repetidos.
    $fileNameArrayRepeated = []; // Nomes de arquivos repetidos.
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]["name"][0] != "") {
        for ($i = 0; $i < count($_FILES[$inputName]["name"]); $i++) {
            $fileName = $_FILES[$inputName]["name"][$i];
            $filePath = __DIR__ . "/uploads/" . basename($fileName);
            move_uploaded_file($_FILES[$inputName]["tmp_name"][$i], $filePath);
            $hash = hash_file($hashAlgorithm, $filePath);
            if ($i == 0) {
                $hashArray[] = $hash;
                $fileNameArray[] = $fileName;
            } else if (!in_array($hash, $hashArray)) {
                $hashArray[] = $hash;
                $fileNameArray[] = $fileName;
            } else {
                for ($j = 0; $j < count($hashArray); $j++) {
                    if ($hashArray[$j] == $hash) {
                        $fileNameArrayRepeated[] = [$fileName, $fileNameArray[$j]];
                    }
                }
            }
            unlink($filePath);
        }
    }
    ?>
    <form enctype="multipart/form-data" action="index.php" method="POST">
        <!--<input type="hidden" name="MAX_FILE_SIZE" value="30000" />-->
        <label for="userfile">Enviar arquivos para análise:</label>
        <input id="userfile" name="userfiles[]" type="file" multiple="multiple" />
        <input type="submit" value="Enviar arquivo" />
    </form>
    <?php
    if (count($fileNameArrayRepeated) > 0) {
        echo "&nbsp;<h2>Arquivos repetidos:</h2>";
        echo "<ul>";
        foreach ($fileNameArrayRepeated as $fileName) {
            echo "<li><h3>" . $fileName[0] . " | " . $fileName[1] . "</h3></li>";
        }
        echo "</ul>";
    } else if (isset($_FILES[$inputName]) && $_FILES[$inputName]["name"][0] != "") {
        echo "&nbsp;<h2>Nenhum arquivo repetido.</h2>";
    } else if (isset($_FILES[$inputName])) {
        echo "&nbsp;<h2>Nenhum arquivo enviado.</h2>";
    }
    ?>
</body>
</html>
