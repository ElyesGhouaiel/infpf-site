<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();
        
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
        
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUsername());
        
        $user->setPassword('hashedpassword');
        $this->assertEquals('hashedpassword', $user->getPassword());
    }

    public function testGetRolesAlwaysIncludesRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetRolesWithAdminRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        // Should not throw any exception
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testRolesAreUnique(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_ADMIN']);

        $roles = $user->getRoles();
        
        // Roles should be unique
        $this->assertEquals(count($roles), count(array_unique($roles)));
    }
}
