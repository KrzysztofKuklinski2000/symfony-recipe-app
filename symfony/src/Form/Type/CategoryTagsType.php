<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\CategoriesToCollectionTransformer;

class CategoryTagsType extends AbstractType {
    public function __construct(
        private CategoriesToCollectionTransformer $transformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent(): string {
        return TextType::class;
    }
}
