<?php

namespace LibraryBundle\Twig;

class LibraryExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('show_img', array($this, 'showImgFunction')),
        );
    }
    public function showImgFunction($width = 262, $height = 350)
    {
        return 'width='.$width.' '.'height='.$height;
    }
}
