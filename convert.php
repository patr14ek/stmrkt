<?php
/**
 * Created by JetBrains PhpStorm.
 * User: patr1ck
 * Date: 06.06.13
 * Time: 12:45
 * To change this template use File | Settings | File Templates.
 */

$comments_pattern = '/(\/\*).*?(\*\/)|(\/\/).*?(\n)|(<!--).*?(-->)/s';
$language_pattern = '/[а-яА-ЯёЁ]/s';


//используем функцию для определения кодировки
function detectEncoding( $string )
{
    static $list = array( 'utf-8', 'windows-1251' );

    foreach ( $list as $item ){

        $sample = iconv( $item, $item, $string );
        if ( md5( $sample ) == md5( $string ) )
            return $item;
    }
    return null;
}

function changeEncoding( $path )
{

    foreach ( glob( $path . '/*.php' ) as $file ){

        echo "  $file ... ";

        $content = file_get_contents( $file );

        //выбираем те файлы, которые не utf-8
        if ( detectEncoding( $content ) == 'windows-1251' ){

            global $comments_pattern;
            global $language_pattern;

            // убираем все комментарии из контента и записываем в новую переменную
            $content_without_comments = preg_replace( $comments_pattern, "\n", $content );

            // проверяем наличие русских букв в контенте без комментов. если они есть, то возвращается 1, если нет - 0
            if( preg_match( $language_pattern, $content_without_comments ) == 0 ) {

                $content = iconv( 'windows-1251', 'UTF-8', $content );
                file_put_contents( $file, $content );
                echo "[win1251] converted\n";

            } else {

                echo "[win1251] passed\n";
                break;
            }

        }
        else {
            echo "[UTF-8] passed\n";
        }
        global $files_count;
        $files_count++;
    }

}

function recursiveSearch( $path )
{
    $local_path = "$path";

    $dir = opendir( $path ); // открываем папку
    changeEncoding( $path ); // выполняем преобразование в текущей папке

    while ( false !== ( $entry = readdir( $dir ) ) ){
        $test_file = "$local_path/$entry";

        // какая-то наркомания, но на php.net написано, что надо так перебирать файлы
        if ( is_dir( $test_file ) ){ // если папка, то выполнить эту же функцию в этой папке
            if ( $entry != ".." && $entry != '.' ){ // выводятся ещё и "." с "..", их игнорить
                echo "$test_file\n";
                recursiveSearch( $test_file );
                global $folders_count;
                $folders_count++;

            }
        }

    }

    closedir( $dir ); // закрываем папку
}

$files_count = 0;
$folders_count = 0;
// отправляем в функцию первоначальную папку (текущую)
recursiveSearch( '.' );
echo "\n";
echo "Files passed: $files_count\n";
echo "Folders passed: $folders_count\n";