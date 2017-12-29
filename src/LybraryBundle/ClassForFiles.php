<?php
/**
 * Created by PhpStorm.
 * User: ser
 * Date: 12.12.17
 * Time: 16:35
 */

namespace LybraryBundle;

use Symfony\Component\Filesystem\Filesystem;

class ClassForFiles
{
    public static function RemoveFile($cover, $book)
    {
        $fs = new Filesystem();
        if ($cover)
        $fs->remove($cover);
        if ($book)
        $fs->remove($book);
    }



    public static function RenameFile($file, $directory)
    {
        /*if ($file) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $directory,
                $fileName
            );
            return $fileName;
        }*/
    }

}