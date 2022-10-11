<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Очистка от мета-тегов</title>
</head>

<body>
    <main>
        <div class="info-block">
            <h2>Очистка html-файлов от мета-тегов</h2><br>
            <hr><br>
            <p>
            <ol>
                <li>Выберите файл(ы)</li>
                <li>Укажите через запятую, какие теги очищать</li>
                <li>Нажмите кнопку [Очистить]</li>
            </ol>
            </p><br>
            <hr><br>
            <p>Очищенный файл будет записан в директорию <b>/data/</b> с именем
            <pre>clear-xxxxxxxx.xxxx</pre>
            </p>
            
        </div>
        <?php if (isset($_GET['success'])) { ?>
            <div class="info-block">
                <h3>Файл успешно очищен</h3><br>
                <hr><br>
                <p><a href="/">Выбрать еще файлы</a> </p>
            </div>
        <?php } elseif (isset($_GET['error'])) { ?>
            <div class="info-block">
                <h3>Произошла ошибка при очистке файла</h3><br>
                <hr><br>
                <p><a href="/">Попробовать еще раз</a> </p>
            </div>
        <?php } else { ?>
            <div class="info-block">
                <form action="remover.php" method="post" class="data-form" enctype="multipart/form-data">
                    <label class="lbl" for="file">Выбор файлов для очищения:</label>
                    <input type="file" name="files[]" id="file-drop" class="lbl" multiple required accept=".htm, .html, .php">
                    <label class="lbl" for="tags">Очищать мета-теги:</label>
                    <input name="tags" type="text" class="inpt" placeholder="..." autofocus value="title, description, keywords">
                    <span></span>
                    <input name="submit" type="submit" value="Очистить" class="btn">
                </form>
            </div>
        <?php } ?>
    </main>
</body>

</html>