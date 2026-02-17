<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\User;
use App\Enum\DietaryTag;
use App\Enum\Difficulty;
use App\Enum\Unit;
use App\Form\DataTransformer\CategoriesToCollectionTransformer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pl_PL');
        $categoryTransformer = new CategoriesToCollectionTransformer($this->entityManager);

        $users = [];

        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setIsVerified(true);
        $manager->persist($admin);
        $users[] = $admin;

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->firstName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
        }

        $liquids = [
            'Mleko 3.2%','Woda','Olej rzepakowy','Oliwa z oliwek','Śmietanka 30%','Rosół','Wino białe','Sos sojowy','Jogurt naturalny','Kefir'
        ];

        $solids = [
            'Pierś z kurczaka','Ryż basmati','Makaron penne','Ziemniaki','Marchew','Cebula','Czosnek','Pomidor',
            'Ogórek','Papryka czerwona','Jajko','Ser gouda','Masło', 'Chleb razowy', 'Mąka pszenna', 'Cukier', 'Sól', 'Jabłko', 'Wołowina', 'Schab', 'Łosoś', 'Pieczarki', 'Szpinak', 'Brokuł', 'Kasza gryczana'
        ];

        for ($i = 0; $i < 20; $i++) {
            $recipe = new Recipe();

            $title = $faker->randomElement(['Pyszna', 'Domowa', 'Szybka', 'Wykwintna']) . ' ' .$faker->randomElement(['Zupa', 'Zapiekanka', 'Sałatka', 'Pasta', 'Pieczeń', 'Tarta']) . ' ' .$faker->randomElement(['Wiosenna', 'Babuni', 'Szefa Kuchni', 'Fit', 'na Ostro']);

            $recipe->setTitle($title);
            $recipe->setInstructions($faker->paragraphs(3, true));
            $recipe->setPreparationTime($faker->numberBetween(15, 120));
            $recipe->setServings($faker->numberBetween(1, 6));
            $recipe->setAuthor($faker->randomElement($users));
            $recipe->setDifficulty($faker->randomElement(Difficulty::cases()));

            $randomTags = $faker->randomElements(DietaryTag::cases(), $faker->numberBetween(0, 2));
            $recipe->setDietaryTags(array_map(fn($t) => $t->value, $randomTags));

            $randomWords = array_map(fn($w) => ucfirst($w), $faker->words($faker->numberBetween(1, 2)));
            $categories = $categoryTransformer->reverseTransform(implode(', ', $randomWords));
            foreach ($categories as $category) {
                $recipe->addCategory($category);
            }

            $recipe->setCalculateNutrition(true);
            $numIngredients = $faker->numberBetween(3, 8);

            for ($j = 0; $j < $numIngredients; $j++) {
                $ingredient = new RecipeIngredient();

                $isLiquid = $faker->boolean(20);

                if ($isLiquid) {
                    $name = $faker->randomElement($liquids);
                    $ingredient->setName($name);

                    $unit = $faker->randomElement([Unit::LITRE, Unit::MILLILITER]);
                    $ingredient->setUnit($unit);

                    if ($unit === Unit::LITRE) {
                        $ingredient->setQuantity($faker->randomFloat(1, 0.1, 1.5));
                    } else {
                        $ingredient->setQuantity($faker->numberBetween(50, 500));
                    }

                    if (str_contains(strtolower($name), 'olej') || str_contains(strtolower($name), 'oliwa')) {
                        $ingredient->setNutritionFactor(0.1);
                    } else {
                        $ingredient->setNutritionFactor(1.0);
                    }

                } else {
                    $name = $faker->randomElement($solids);
                    $ingredient->setName($name);

                    $unit = $faker->randomElement([Unit::KILOGRAM, Unit::GRAM]);
                    $ingredient->setUnit($unit);

                    if ($unit === Unit::KILOGRAM) {
                        $ingredient->setQuantity($faker->randomFloat(2, 0.1, 1.5));
                    } else {
                        $ingredient->setQuantity($faker->numberBetween(10, 800));
                    }

                    $ingredient->setNutritionFactor(1.0);
                }

                $recipe->addRecipeIngredient($ingredient);
            }

            $recipe->setKcal($faker->numberBetween(250, 1200));
            $recipe->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
