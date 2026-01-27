<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires basiques pour MetierService
 */
class MetierServiceTest extends TestCase
{
    public function testServiceClassExists(): void
    {
        $this->assertTrue(class_exists(\App\Service\MetierService::class));
    }

    public function testServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(\App\Service\MetierService::class);
        
        $this->assertTrue($reflection->hasMethod('getMetierBySlug'));
        $this->assertTrue($reflection->hasMethod('slugify'));
        $this->assertTrue($reflection->hasMethod('isMetiersEnabled'));
        $this->assertTrue($reflection->hasMethod('findFormationsByMetier'));
        $this->assertTrue($reflection->hasMethod('generateJsonLd'));
    }

    public function testConfigFileExists(): void
    {
        $configPath = __DIR__ . '/../../config/metiers.yaml';
        
        $this->assertFileExists($configPath, 'Le fichier de configuration metiers.yaml doit exister');
    }
}
