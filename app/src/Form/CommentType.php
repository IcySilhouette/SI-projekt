<?php

/**
 * This file is part of the Symfony application.
 *
 * (c) Comment Form Type
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommentType.
 */
class CommentType extends AbstractType
{
    /**
     * Builds the comment form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nick', TextType::class, [
                'label' => 'Twój nick (dla niezalogowanych)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Wpisz swój nick',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Twój e-mail (dla niezalogowanych)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Wpisz swój adres e-mail',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Dodaj komentarz',
                'attr' => [
                    'placeholder' => 'Napisz coś mądrego...',
                    'rows' => 3,
                ],
            ]);
    }

    /**
     * Configures the default options for this form.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
