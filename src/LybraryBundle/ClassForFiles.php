<?php

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
}