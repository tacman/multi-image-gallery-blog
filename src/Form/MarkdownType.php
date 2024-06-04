<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkdownType extends TextareaType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'attr'     => [
                'class' => 'markdown-textarea',
            ],
        ]);
    }
}
