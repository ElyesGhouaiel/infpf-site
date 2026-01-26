<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertCount(0, $errors, 'Un utilisateur valide ne devrait pas avoir d\'erreurs');
    }

    public function testInvalidEmail(): void
    {
        $user = new User();
        $user->setEmail('invalid-email');
        $user->setUsername('testuser');
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertGreaterThan(0, count($errors), 'Un email invalide devrait générer une erreur');
    }

    public function testBlankEmail(): void
    {
        $user = new User();
        $user->setEmail('');
        $user->setUsername('testuser');
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertGreaterThan(0, count($errors), 'Un email vide devrait générer une erreur');
    }

    public function testUsernameTooShort(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('ab'); // Moins de 3 caractères
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertGreaterThan(0, count($errors), 'Un username trop court devrait générer une erreur');
    }

    public function testUsernameInvalidCharacters(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test user!'); // Caractères spéciaux non autorisés
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertGreaterThan(0, count($errors), 'Un username avec caractères spéciaux devrait générer une erreur');
    }

    public function testValidUsernameWithUnderscoreAndDash(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test_user-123');
        $user->setPassword('hashedpassword123');

        $errors = $this->validator->validate($user);
        
        $this->assertCount(0, $errors, 'Un username avec underscore et tiret devrait être valide');
    }

    public function testGetRolesAlwaysIncludesRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_USER', $roles, 'ROLE_USER devrait toujours être présent');
    }

    public function testGetRolesWithAdminRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles, 'ROLE_ADMIN devrait être présent');
        $this->assertContains('ROLE_USER', $roles, 'ROLE_USER devrait toujours être présent');
    }
}
