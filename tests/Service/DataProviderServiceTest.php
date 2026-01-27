<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires basiques - les tests d'integration sont dans le dossier Functional
 */
class DataProviderServiceTest extends TestCase
{
    public function testServiceClassExists(): void
    {
        $this->assertTrue(class_exists(\App\Service\DataProviderService::class));
    }

    public function testServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(\App\Service\DataProviderService::class);
        
        $this->assertTrue($reflection->hasMethod('getCategories'));
        $this->assertTrue($reflection->hasMethod('getFormations'));
    }
}
