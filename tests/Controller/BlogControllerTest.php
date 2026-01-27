<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testBlogIndexIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
    }

    public function testBlogIndexContainsBlogContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        
        // La page blog devrait contenir du contenu
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }

    public function testBlogPageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }
}
