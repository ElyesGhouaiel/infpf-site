<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testBlogListIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
    }

    public function testBlogListHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testBlogListContainsContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }
}
