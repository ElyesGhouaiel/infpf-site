<?php

namespace App\Tests\Controller;

use App\Tests\Functional\WebTestCaseWithFixtures;

class BlogControllerTest extends WebTestCaseWithFixtures
{
    public function testBlogListIsAccessible(): void
    {
        $this->client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
    }

    public function testBlogListHasTitle(): void
    {
        $crawler = $this->client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testBlogListContainsContent(): void
    {
        $crawler = $this->client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }
}
