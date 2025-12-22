<?php

namespace App\Form\DataTransformer;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesToCollectionTransformer implements DataTransformerInterface {
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

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

        $names = explode(',', $categories);

        foreach($names as $name) {
            $name = trim($name);

            if(!$name) continue;
            //Szukamy w tabeli category kategorii o nazwie $name
            $category = $this->em->getRepository(Category::class)->findOneBy(['name' => $name]);

            if(!$category) {
                $category = new Category();
                $category->setName($name);
                // $category->setSlug($this->slugger->slug($name)->lower());
            }

            //Dodajemy tylko unikaty
            if(!$collection->contains($category)) {
                $collection->add($category);
            }
        }

        return $collection;
    }
}
