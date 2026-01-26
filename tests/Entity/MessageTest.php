<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidMessage(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('jean.dupont@example.com');
        $message->setNumero('06 12 34 56 78');
        $message->setContent('Bonjour, je souhaite avoir des informations.');
        $message->setRequestType('renseignement');
        $message->setPreferredMode('distance');

        $errors = $this->validator->validate($message);
        
        $this->assertCount(0, $errors, 'Un message valide ne devrait pas avoir d\'erreurs');
    }

    public function testBlankName(): void
    {
        $message = new Message();
        $message->setName('');
        $message->setEmail('test@example.com');

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un nom vide devrait générer une erreur');
    }

    public function testNameTooShort(): void
    {
        $message = new Message();
        $message->setName('A'); // Moins de 2 caractères
        $message->setEmail('test@example.com');

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un nom trop court devrait générer une erreur');
    }

    public function testInvalidEmail(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('invalid-email');

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un email invalide devrait générer une erreur');
    }

    public function testValidPhoneNumbers(): void
    {
        $validNumbers = [
            '0612345678',
            '06 12 34 56 78',
            '+33612345678',
            '+33 6 12 34 56 78',
            '01.23.45.67.89',
        ];

        foreach ($validNumbers as $number) {
            $message = new Message();
            $message->setName('Jean Dupont');
            $message->setEmail('test@example.com');
            $message->setNumero($number);

            $errors = $this->validator->validate($message);
            
            $this->assertCount(0, $errors, "Le numéro '$number' devrait être valide");
        }
    }

    public function testInvalidPhoneNumber(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('test@example.com');
        $message->setNumero('abc123'); // Lettres non autorisées

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un numéro avec des lettres devrait générer une erreur');
    }

    public function testInvalidRequestType(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('test@example.com');
        $message->setRequestType('invalid_type');

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un type de demande invalide devrait générer une erreur');
    }

    public function testValidRequestTypes(): void
    {
        $validTypes = ['renseignement', 'devis'];

        foreach ($validTypes as $type) {
            $message = new Message();
            $message->setName('Jean Dupont');
            $message->setEmail('test@example.com');
            $message->setRequestType($type);

            $errors = $this->validator->validate($message);
            
            // Vérifier qu'il n'y a pas d'erreur liée au requestType
            $requestTypeErrors = array_filter(
                iterator_to_array($errors),
                fn($e) => $e->getPropertyPath() === 'requestType'
            );
            
            $this->assertCount(0, $requestTypeErrors, "Le type '$type' devrait être valide");
        }
    }

    public function testInvalidPreferredMode(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('test@example.com');
        $message->setPreferredMode('invalid_mode');

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un mode préféré invalide devrait générer une erreur');
    }

    public function testContentTooLong(): void
    {
        $message = new Message();
        $message->setName('Jean Dupont');
        $message->setEmail('test@example.com');
        $message->setContent(str_repeat('a', 5001)); // Plus de 5000 caractères

        $errors = $this->validator->validate($message);
        
        $this->assertGreaterThan(0, count($errors), 'Un contenu trop long devrait générer une erreur');
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $message = new Message();
        
        $this->assertNotNull($message->getCreatedAt(), 'createdAt devrait être défini automatiquement');
        $this->assertInstanceOf(\DateTimeInterface::class, $message->getCreatedAt());
    }
}
