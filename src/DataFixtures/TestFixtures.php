<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Formation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixtures de test pour les tests automatises
 * Groupe: test (charger avec --group=test)
 */
class TestFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Creer un utilisateur admin pour les tests
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setUsername('testadmin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'testpassword123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('user-admin', $admin);

        // 2. Creer un utilisateur standard
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setUsername('testuser');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'testpassword123'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $this->addReference('user-standard', $user);

        // 3. Creer des categories
        $categories = [];
        $categoryNames = [
            'Intelligence Artificielle' => 'Formations sur l\'IA et le Machine Learning',
            'Marketing Digital' => 'Formations marketing et communication digitale',
            'Developpement Web' => 'Formations developpement et programmation',
        ];

        foreach ($categoryNames as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($description);
            $manager->persist($category);
            $categories[$name] = $category;
        }

        // 4. Creer des formations
        $formationData = [
            [
                'name' => 'Formation IA Generative',
                'description' => 'Apprenez a maitriser l\'IA generative pour le marketing',
                'duree' => '35 heures',
                'price' => 2500,
                'category' => 'Intelligence Artificielle',
                'niveau' => 'Debutant',
            ],
            [
                'name' => 'Formation Marketing Digital Avance',
                'description' => 'Strategies avancees de marketing digital',
                'duree' => '21 heures',
                'price' => 1800,
                'category' => 'Marketing Digital',
                'niveau' => 'Intermediaire',
            ],
            [
                'name' => 'Formation Developpement Web',
                'description' => 'Creez des sites web modernes',
                'duree' => '70 heures',
                'price' => 3500,
                'category' => 'Developpement Web',
                'niveau' => 'Debutant',
            ],
        ];

        foreach ($formationData as $index => $data) {
            $formation = new Formation();
            $formation->setNameFormation($data['name']);
            $formation->setDescriptionFormation($data['description']);
            $formation->setDureeFormation($data['duree']);
            $formation->setPriceFormation($data['price']);
            $formation->setCategory($categories[$data['category']]);
            $formation->setNiveau($data['niveau']);
            $formation->setLangue('Francais');
            $formation->setLieu('A distance');
            $manager->persist($formation);
            $this->addReference('formation-' . $index, $formation);
        }

        // 5. Creer des articles de blog
        $blogData = [
            [
                'title' => 'Introduction a l\'IA Generative',
                'content' => 'L\'intelligence artificielle generative revolutionne le monde du travail...',
                'author' => 'INFPF',
                'status' => Blog::STATUS_PUBLISHED,
            ],
            [
                'title' => 'Les tendances du marketing digital en 2024',
                'content' => 'Decouvrez les nouvelles tendances du marketing digital...',
                'author' => 'INFPF',
                'status' => Blog::STATUS_PUBLISHED,
            ],
        ];

        foreach ($blogData as $index => $data) {
            $blog = new Blog();
            $blog->setTitleOne($data['title']);
            $blog->setContentOne($data['content']);
            $blog->setAuthor($data['author']);
            $blog->setStatus($data['status']);
            $blog->setPublishedAt(new \DateTimeImmutable());
            $blog->setShortDesc(substr($data['content'], 0, 100));
            $manager->persist($blog);
            $this->addReference('blog-' . $index, $blog);
        }

        $manager->flush();
    }
}
