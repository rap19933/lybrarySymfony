<?php

namespace LybraryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array("label" => "Название книги"))
            ->add('author', TextType::class, array("label" => "Автор книги"))
            ->add('cover', FileType::class,
                array('label' => "Обложка книги",
                      "data_class" => null,
                      "required" => false,
                      'constraints' => array(
                          new File(array(
                              'mimeTypes' => array('image/png', 'image/jpeg'),
                              'mimeTypesMessage' => 'Выбирете файл формата png или jpg',
                          ))
                      ),
                      'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px'],
                ))
            ->add('bookFile', FileType::class,
                array('label' => "Файл книги",
                      "data_class" => null,
                      "required" => false,
                      'constraints' => array(
                          new File(array(
                              'maxSize' => '5M',
                              'maxSizeMessage' => 'Выбирете файл менее 5 Мб',
                              'notFoundMessage' => 'выбирете файл',
                          ))
                      ),
                      'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-bottom: 20px'],
            ))
            ->add('dateRead', DateType::class, array("widget" => "single_text", "label" => "Дата прочтения"))
            ->add('allowDownload', CheckboxType::class, array("label" => "Разрешить скачивание", "required" => false));
    }
    
    /**
     * {@inheritdoc}
     */
   /* public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LybraryBundle\Entity\Book'
        ));
    }*/

    /**
     * {@inheritdoc}
     */
   /* public function getBlockPrefix()
    {
        return 'lybrarybundle_book';
    }*/


}
