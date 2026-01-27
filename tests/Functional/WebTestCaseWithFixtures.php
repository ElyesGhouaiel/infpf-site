<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Formation;
use App\Entity\Blog;

/**
 * Classe de base pour les tests fonctionnels avec fixtures
 */
abstract class WebTestCaseWithFixtures extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected ?EntityManagerInterface $entityManager = null;
    private static bool $fixturesLoaded = false;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        // Charger les fixtures une seule fois par suite de tests
        if (!self::$fixturesLoaded) {
            $this->loadFixtures();
            self::$fixturesLoaded = true;
        }
    }

    private function loadFixtures(): void
    {
        // Creer le schema si necessaire
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        
        try {
            $schemaTool->dropSchema($metadata);
        } catch (\Exception $e) {
            // Ignore si les tables n'existent pas
        }
        
        $schemaTool->createSchema($metadata);
        
        // Creer les donnees de test
        $this->createTestData();
    }

    private function createTestData(): void
    {
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        
        // 1. Utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setUsername('testadmin');
        $admin->setPassword($passwordHasher->hashPassword($admin, 'testpassword123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($admin);

        // 2. Utilisateur standard
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setUsername('testuser');
        $user->setPassword($passwordHasher->hashPassword($user, 'testpassword123'));
        $this->entityManager->persist($user);

        // 3. Categories
        $categoryIA = new Category();
        $categoryIA->setName('Intelligence Artificielle');
        $categoryIA->setDescription('Formations IA');
        $this->entityManager->persist($categoryIA);

        $categoryMarketing = new Category();
        $categoryMarketing->setName('Marketing Digital');
        $categoryMarketing->setDescription('Formations marketing');
        $this->entityManager->persist($categoryMarketing);

        // 4. Formations
        $formation1 = new Formation();
        $formation1->setNameFormation('Formation IA Generative');
        $formation1->setDescriptionFormation('Formation complete sur l\'IA generative');
        $formation1->setDureeFormation('35 heures');
        $formation1->setPriceFormation(2500);
        $formation1->setCategory($categoryIA);
        $formation1->setNiveau('Debutant');
        $this->entityManager->persist($formation1);

        $formation2 = new Formation();
        $formation2->setNameFormation('Marketing Digital Avance');
        $formation2->setDescriptionFormation('Strategies avancees de marketing');
        $formation2->setDureeFormation('21 heures');
        $formation2->setPriceFormation(1800);
        $formation2->setCategory($categoryMarketing);
        $this->entityManager->persist($formation2);

        // 5. Articles de blog
        $blog1 = new Blog();
        $blog1->setTitleOne('Introduction a l\'IA');
        $blog1->setContentOne('Contenu de l\'article sur l\'IA...');
        $blog1->setAuthor('INFPF');
        $blog1->setStatus(Blog::STATUS_PUBLISHED);
        $blog1->setPublishedAt(new \DateTimeImmutable());
        $this->entityManager->persist($blog1);

        $blog2 = new Blog();
        $blog2->setTitleOne('Tendances Marketing 2024');
        $blog2->setContentOne('Les tendances du marketing digital...');
        $blog2->setAuthor('INFPF');
        $blog2->setStatus(Blog::STATUS_PUBLISHED);
        $blog2->setPublishedAt(new \DateTimeImmutable());
        $this->entityManager->persist($blog2);

        $this->entityManager->flush();
    }

    protected function loginAsAdmin(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@test.com']);
        
        if ($admin) {
            $this->client->loginUser($admin);
        }
    }

    protected function loginAsUser(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'user@test.com']);
        
        if ($user) {
            $this->client->loginUser($user);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}
