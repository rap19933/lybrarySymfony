<?php

namespace LybraryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array("label" => "Название книги"))
            ->add('author')
            //->add('cover')
            ->add('cover', FileType::class)
            ->add('bookFile', FileType::class,
                array('label' => "Файл книги",
                      "data_class" => null,
                      "required" => false,
                      'constraints' => array(
                          new File(array(
                              'maxSize' => '5M',
                              'maxSizeMessage' => 'Слишком большой файл (max: 5Мб)',
                          ))
                      )
            ))
            ->add('dateRead', DateType::class, array("widget" => "single_text", "label" => "Дата прочтения"))
            ->add('allowDownload', CheckboxType::class, array("label" => "Разрешить скачивание", "required" => false))
            ->add('user');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LybraryBundle\Entity\Book'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lybrarybundle_book';
    }


}
