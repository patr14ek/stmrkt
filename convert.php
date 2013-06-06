<?php
/**
 * Created by JetBrains PhpStorm.
 * User: patr1ck
 * Date: 06.06.13
 * Time: 12:45
 * To change this template use File | Settings | File Templates.
 */

foreach (glob('*.php') as $file) {

    $amount = 0;
    $content = file_get_contents($file);

    //выбираем те файлы, которые не utf-8
    if (mb_detect_encoding( $file, 'UTF-8', false )) {

        $lexem = token_get_all( $content );
        foreach ( $lexem as $sType => $sString ) {

            if ( $sType == T_INLINE_HTML ) {
                continue;
            }
            //если есть русские символы, выходим из цикла
            if ( preg_match( '/[А-Яа-я]/', $sType ) ) {

                $flag = true;
                break;
            }

            if ( $flag )
                continue;
            else {
                $content = iconv( 'windows-1251', 'UTF-8', $content );
                $file = file_put_contents( $file, $content );
                $amount++;
            }

        }

    }
    echo $amount;
}