<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $message = new Message();
        
        $message->setName('Jean Dupont');
        $this->assertEquals('Jean Dupont', $message->getName());
        
        $message->setEmail('jean@example.com');
        $this->assertEquals('jean@example.com', $message->getEmail());
        
        $message->setNumero('0612345678');
        $this->assertEquals('0612345678', $message->getNumero());
        
        $message->setContent('Test content');
        $this->assertEquals('Test content', $message->getContent());
    }

    public function testRequestType(): void
    {
        $message = new Message();
        
        $message->setRequestType('renseignement');
        $this->assertEquals('renseignement', $message->getRequestType());
        
        $message->setRequestType('devis');
        $this->assertEquals('devis', $message->getRequestType());
    }

    public function testPreferredMode(): void
    {
        $message = new Message();
        
        $message->setPreferredMode('distance');
        $this->assertEquals('distance', $message->getPreferredMode());
        
        $message->setPreferredMode('presentiel');
        $this->assertEquals('presentiel', $message->getPreferredMode());
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $message = new Message();
        
        $this->assertNotNull($message->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $message->getCreatedAt());
    }

    public function testFormationIdAndName(): void
    {
        $message = new Message();
        
        $message->setFormationId(123);
        $this->assertEquals(123, $message->getFormationId());
        
        $message->setFormationName('Formation Test');
        $this->assertEquals('Formation Test', $message->getFormationName());
    }

    public function testIdIsNullBeforePersist(): void
    {
        $message = new Message();
        
        $this->assertNull($message->getId());
    }
}
