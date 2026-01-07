<?php

namespace App\Form\DataTransformer;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class CategoriesToCollectionTransformer implements DataTransformerInterface {
    public function __construct(
        private EntityManagerInterface $em,
    ){}

    public function transform(mixed $categories): string {

        if(empty($categories)) return '';

        $name = [];
        foreach($categories as $category) {
            $name[] = $category->getName();
        }

        return implode(',', $name);
    }

    public function reverseTransform(mixed $categories): Collection {
        $collection = new ArrayCollection();

        if(!$categories) return $collection;

        $names = array_filter(array_map('trim', explode(',', $categories)));
        $names = array_unique($names);

        if(empty($names)) return $collection;

        $existingCategories = $this->em->getRepository(Category::class)->findBy(['name' => $names]);

        $existingCategoriesMap = [];
        foreach($existingCategories as $existingCategory) {
            $existingCategoriesMap[strtolower($existingCategory->getName())] = $existingCategory;
        }

        foreach($names as $name) {
            $lowerName = strtolower($name);

            if(isset($existingCategoriesMap[$lowerName])) {
                $category = $existingCategoriesMap[$lowerName];
            } else {
                $category = new Category();
                $category->setName($name);
            }

            if(!$collection->contains($category)) {
                $collection->add($category);
            }
        }

        return $collection;
    }
}
