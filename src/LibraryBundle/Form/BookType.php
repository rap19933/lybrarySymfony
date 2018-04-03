<?php

namespace LibraryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BookType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array("label" => 'form.name_book'))
            ->add('author', TextType::class, array("label" => 'form.author_book'))
            ->add(
                'cover',
                FileType::class,
                array(
                    'label' => 'form.cover_book',
                    'data_class' => null,
                    'required' => false,
                    'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px']
                    )
            )
            ->add(
                'bookFile',
                FileType::class,
                array(
                    'label' => 'form.file_book',
                    'data_class' => null,
                    'required' => false,
                    'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px']
                    )
            )
            ->add('dateRead', DateType::class,  array("widget" => "single_text", "label" => 'form.date_read'))
            ->add('allowDownload', CheckboxType::class, array("label" => 'form.allow_download', "required" => false));
    }
}
