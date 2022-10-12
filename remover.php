<?php

const FILE_DIR = 'data/';

// Итератор с фильтрацией по мета-тегам
class HtmlFilter extends FilterIterator
{
    private $filters;

    public function __construct(Iterator $iterator, $filter)
    {
        parent::__construct($iterator);
        $this->filters = $filter;
    }

    public function accept()
    {
        $html = $this->getInnerIterator()->current();
        foreach ($this->filters as $filter) {
            if (preg_match($filter, $html)) {
                return false;
                break;
            }
        }
        return true;
    }
}

// Функция удаляет из текста совпадения типа <$tags>...</$tags> и <meta name="$tags"....>
function rem_html_tags($tags, $str)
{
    $html = array();
    foreach ($tags as $tag) {
        $html[] = '/(<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>)/';
        $html[] = '/<meta name="' . $tag . '".*?>/';
    }

    //print_r($html);
    //die;

    $data = preg_replace($html, '', $str);

    return $data;
}

// Очистка тегов без итератора (оставляект пустые строки вместо тегов)
function removeType1($fileName, $tags)
{
    $filePath = FILE_DIR . basename($fileName);
    $filePathNew = FILE_DIR . 'clear-1_' . basename($fileName);

    // читаем файл в строку
    $fileContent =  file_get_contents($filePath);

    // удаляем ненужные теги array('title', 'description', 'keywords')
    $fileContentNew = rem_html_tags($tags, $fileContent, 1);

    // удалим, если такой уже есть
    if (file_exists($filePathNew)) {
        unlink($filePathNew);
    }
    // сохраняем файл в /data/clear-1_$filename
    file_put_contents($filePathNew, $fileContentNew, LOCK_EX);
}

// Очистка тегов с итератором
function removeType2($fileName, $tags)
{
    $filePath = FILE_DIR . basename($fileName);
    $filePathNew = FILE_DIR . 'clear-2_' . basename($fileName);

    $object = new ArrayObject(file($filePath));

    //$tags = array('title', 'description', 'keywords');
    $filter = array();
    
    foreach ($tags as $tag) {
        $filter[] = '/(<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>)/';
        $filter[] = '/<meta name="' . $tag . '".*?>/';
    }

    //print_r($filter);
    //die;

    $iterator = new HtmlFilter($object->getIterator(), $filter);

    // сохраняем файл в /data/clear-1_$filename
    $html = [];
    foreach ($iterator as $result) {
        $html[] = $result;
    }
    file_put_contents($filePathNew, $html, LOCK_EX);
}

/* ------------------------------------------ тело программы --------------------------------------- */
// проверяем переданные параметры
if (isset($_POST['submit1'])) {
    $type = 1;
} elseif (isset($_POST['submit2'])) {
    $type = 2;
} else {
    echo 'Неверный вызов ресурса!';
    die;
}
if (isset($_POST['tags'])) {
    $param = $_POST['tags'];
    $param = str_replace(' ', '', $param);
    $tags = explode(',', $param);
} else {
    echo 'Неверный вызов ресурса!';
    die;
}
if (empty($_FILES)) {
    echo 'Не указаны файлы для очистки!';
    die;
}

$errors = [];

// цикл по загружаемым файлам
for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

    $fileName = $_FILES['files']['name'][$i];

    $filePath = FILE_DIR . basename($fileName);

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

    // Очищаем выбранным методом
    if ($type === 1) {
        removeType1($filePath, $tags);
    } elseif ($type === 2) {
        removeType2($filePath, $tags);
    }

    if (!$errors) {
    // Возвращаемся на главную
    header("Location: /?success");
    } else {
        print_r($errors);
    }
}
