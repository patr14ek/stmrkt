<?php
/**
 * Created by JetBrains PhpStorm.
 * User: patr1ck
 * Date: 06.06.13
 * Time: 12:45
 * To change this template use File | Settings | File Templates.
 */

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

foreach ( glob( '*.php' ) as $file ){

    $content = file_get_contents( $file );

    //выбираем те файлы, которые не utf-8
    if ( detectEncoding( $content ) == 'windows-1251' ){

        $lexem = token_get_all( $content );
        foreach ( $lexem as $sType => $sString ){

            if ( $sType == T_INLINE_HTML ){

                continue;
            }
            //если есть русские символы, выходим из цикла
            if ( preg_match( '/[А-Яа-я]/', $sType ) ){

                $flag = true;
                break;
            }

            if ( $flag ){

                continue;
            }
            else{

                $content = iconv( 'windows-1251', 'UTF-8', $content );
                file_put_contents( $file, $content );

            }

        }

    }

}

