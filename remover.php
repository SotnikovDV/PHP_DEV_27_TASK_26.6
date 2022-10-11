<?php

// Функция удаляет из текста совпадения типа <$tags>...</$tags> и <meta name="$tags"....>
function rem_html_tags($tags, $str)
{
    $html = array();
    foreach ($tags as $tag) {
        $html[] = '/(<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>)/';
        $html[] = '/<meta name="' . $tag . '".*?>/';
    }
    $data = preg_replace($html, '', $str);

    return $data;
}


if (isset($_POST['submit'])) {
    $errors = [];

    if (!empty($_FILES)) {

        // цикл по загружаемым файлам
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

            $fileName = $_FILES['files']['name'][$i];

            $filePath = 'data/' . basename($fileName);

            // удалим, если такой уже есть
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Загружаем в папку для загрузок
            if (!move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
                $errors[] = 'Ошибка загрузки файла ' . $fileName;

                continue;
            }
            if (count($errors) > 0) {
                var_dump($errors);
                die;
            }
            // читаем файл в строку
            $fileContent =  file_get_contents($filePath);

            // удаляем ненужные теги
            $fileContentNew = rem_html_tags(array('title', 'description', 'keywords'), $fileContent, 1);

            $filePathNew = 'data/clear-' . basename($fileName);
            // удалим, если такой уже есть
            if (file_exists($filePathNew)) {
                unlink($filePathNew);
            }
            // сохраняем файл в /data/clear-$filename
            file_put_contents($filePathNew, $fileContentNew, LOCK_EX);

            // Возвращаемся на главную
            //            header("Location: /?success");

            /*
            $dom = new DOMDocument;
            @$dom->loadHTML($fileContent);

            //var_dump($dom);

            $output = array();
            */

            //$iterator = new ArrayIterator(file($filePath));

            $a = new ArrayIterator(file($filePath));
            $i = new RegexIterator($a, '/^(test)(\d+)/', RegexIterator::REPLACE);
            $i->replacement = '$2:$1';

            print_r(iterator_to_array($i));

            /*
            foreach($iterator as $key=>$value) {
                echo $key,':', $value,'<br>';
            }
            */
        }
    } else {
        echo 'Файлы не найдены';
    }
}
