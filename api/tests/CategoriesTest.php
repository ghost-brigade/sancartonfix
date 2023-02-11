<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;

class CategoriesTest extends ApiTestCase
{
    public function testGetCollection()
    {
        $response = static::createClient()->request('GET', '/categories', ['auth_bearer' => $GLOBALS['token']]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Category',
            '@id' => '/categories',
            '@type' => 'hydra:Collection',
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(Category::class);
    }

    public function testCreateCategory(): void
    {
        $response = static::createClient()->request('POST', '/categories', ['json' => ['name' => 'Category 1',], 'auth_bearer' => $GLOBALS['token']]);
        $GLOBALS['categoryId'] = $response->toArray()['id'];

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Category::class);
    }

    public function testCreateCategoryInvalid(): void
    {
        $response = static::createClient()->request('POST', '/categories', ['json' => [
            'n@me' => 'Category 1',
        ], 'auth_bearer' => $GLOBALS['token']
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDeleteCategory(): void
    {
        $response = static::createClient()->request('DELETE', '/categories/' . $GLOBALS['categoryId'], ['auth_bearer' => $GLOBALS['token']]);
        $this->assertResponseStatusCodeSame(204);
    }
}
