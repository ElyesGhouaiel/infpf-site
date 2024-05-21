<?php

namespace App\Test\Controller;

use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/blog/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Blog::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Blog index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'blog[title_one]' => 'Testing',
            'blog[publishedAt]' => 'Testing',
            'blog[author]' => 'Testing',
            'blog[content_one]' => 'Testing',
            'blog[title_two]' => 'Testing',
            'blog[content_two]' => 'Testing',
            'blog[title_tree]' => 'Testing',
            'blog[content_tree]' => 'Testing',
            'blog[sous_title_tree]' => 'Testing',
            'blog[sous_content_tree]' => 'Testing',
            'blog[title_for]' => 'Testing',
            'blog[content_for]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Blog();
        $fixture->setTitle_one('My Title');
        $fixture->setPublishedAt('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setContent_one('My Title');
        $fixture->setTitle_two('My Title');
        $fixture->setContent_two('My Title');
        $fixture->setTitle_tree('My Title');
        $fixture->setContent_tree('My Title');
        $fixture->setSous_title_tree('My Title');
        $fixture->setSous_content_tree('My Title');
        $fixture->setTitle_for('My Title');
        $fixture->setContent_for('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Blog');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Blog();
        $fixture->setTitle_one('Value');
        $fixture->setPublishedAt('Value');
        $fixture->setAuthor('Value');
        $fixture->setContent_one('Value');
        $fixture->setTitle_two('Value');
        $fixture->setContent_two('Value');
        $fixture->setTitle_tree('Value');
        $fixture->setContent_tree('Value');
        $fixture->setSous_title_tree('Value');
        $fixture->setSous_content_tree('Value');
        $fixture->setTitle_for('Value');
        $fixture->setContent_for('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'blog[title_one]' => 'Something New',
            'blog[publishedAt]' => 'Something New',
            'blog[author]' => 'Something New',
            'blog[content_one]' => 'Something New',
            'blog[title_two]' => 'Something New',
            'blog[content_two]' => 'Something New',
            'blog[title_tree]' => 'Something New',
            'blog[content_tree]' => 'Something New',
            'blog[sous_title_tree]' => 'Something New',
            'blog[sous_content_tree]' => 'Something New',
            'blog[title_for]' => 'Something New',
            'blog[content_for]' => 'Something New',
        ]);

        self::assertResponseRedirects('/blog/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle_one());
        self::assertSame('Something New', $fixture[0]->getPublishedAt());
        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getContent_one());
        self::assertSame('Something New', $fixture[0]->getTitle_two());
        self::assertSame('Something New', $fixture[0]->getContent_two());
        self::assertSame('Something New', $fixture[0]->getTitle_tree());
        self::assertSame('Something New', $fixture[0]->getContent_tree());
        self::assertSame('Something New', $fixture[0]->getSous_title_tree());
        self::assertSame('Something New', $fixture[0]->getSous_content_tree());
        self::assertSame('Something New', $fixture[0]->getTitle_for());
        self::assertSame('Something New', $fixture[0]->getContent_for());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Blog();
        $fixture->setTitle_one('Value');
        $fixture->setPublishedAt('Value');
        $fixture->setAuthor('Value');
        $fixture->setContent_one('Value');
        $fixture->setTitle_two('Value');
        $fixture->setContent_two('Value');
        $fixture->setTitle_tree('Value');
        $fixture->setContent_tree('Value');
        $fixture->setSous_title_tree('Value');
        $fixture->setSous_content_tree('Value');
        $fixture->setTitle_for('Value');
        $fixture->setContent_for('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/blog/');
        self::assertSame(0, $this->repository->count([]));
    }
}
