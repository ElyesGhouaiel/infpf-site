<?php

namespace App\Test\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/formation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Formation::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Formation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'formation[name_formation]' => 'Testing',
            'formation[description_formation]' => 'Testing',
            'formation[duree_formation]' => 'Testing',
            'formation[price_formation]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Formation();
        $fixture->setName_formation('My Title');
        $fixture->setDescription_formation('My Title');
        $fixture->setDuree_formation('My Title');
        $fixture->setPrice_formation('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Formation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Formation();
        $fixture->setName_formation('Value');
        $fixture->setDescription_formation('Value');
        $fixture->setDuree_formation('Value');
        $fixture->setPrice_formation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'formation[name_formation]' => 'Something New',
            'formation[description_formation]' => 'Something New',
            'formation[duree_formation]' => 'Something New',
            'formation[price_formation]' => 'Something New',
        ]);

        self::assertResponseRedirects('/formation/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName_formation());
        self::assertSame('Something New', $fixture[0]->getDescription_formation());
        self::assertSame('Something New', $fixture[0]->getDuree_formation());
        self::assertSame('Something New', $fixture[0]->getPrice_formation());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Formation();
        $fixture->setName_formation('Value');
        $fixture->setDescription_formation('Value');
        $fixture->setDuree_formation('Value');
        $fixture->setPrice_formation('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/formation/');
        self::assertSame(0, $this->repository->count([]));
    }
}
