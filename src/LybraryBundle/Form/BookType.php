<?php

namespace LybraryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class BookType extends AbstractType
{
    private $translator;

    public function __construct($language)
    {
        $this->translator = new Translator($language);
        $this->translator->addLoader('yaml', new YamlFileLoader());
        $this->translator->addResource(
            'yaml',
            __DIR__ . '/translations/form_trans.' . $language . '.yml',
            $language
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array("label" => $this->translator->trans('name_book')))
            ->add('author', TextType::class, array("label" => $this->translator->trans('author_book')))
            ->add(
                'cover',
                FileType::class,
                array(
                    'label' => $this->translator->trans('cover_book'),
                    'data_class' => null,
                    'required' => false,
                    'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px']
                    )
            )
            ->add(
                'bookFile',
                FileType::class,
                array(
                    'label' => $this->translator->trans('file_book'),
                    'data_class' => null,
                    'required' => false,
                    'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px']
                    )
            )
            ->add(
                'dateRead',
                DateType::class,
                array("widget" => "single_text", "label" => $this->translator->trans('date_read'))
            )
            ->add(
                'allowDownload',
                CheckboxType::class,
                array("label" => $this->translator->trans('allow_download'), "required" => false)
            );
    }
}
