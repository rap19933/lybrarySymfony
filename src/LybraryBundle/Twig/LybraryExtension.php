<?php
/**
 * Created by PhpStorm.
 * User: ser
 * Date: 12.12.17
 * Time: 14:13
 */
namespace LybraryBundle\Twig;

class LybraryExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('show_img', array($this, 'ShowImgFunction')),
        );
    }
    public function ShowImgFunction($width = 262, $height = 350)
    {
        return 'width='.$width.' '.'height='.$height;
    }
}