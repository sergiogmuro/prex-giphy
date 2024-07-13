<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class GiphyTest extends TestCase
{
    use DatabaseMigrations;

    public $mockConsoleOutput = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:client', [
            '--no-interaction' => true,
            '--personal' => null
        ]);

        Passport::actingAs(
            User::factory()->create(),
            ['giphy']
        );
    }

    public function test_can_search_gifs_with_query_string()
    {
        $response = $this->getJson('/api/gif/search?query=cats');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_search_gifs_and_return_ten_results()
    {
        $response = $this->getJson('/api/gif/search?query=cats&offset=0&limit=10');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);

        $data = $response->json()['data'];

        $this->assertCount(10, $data);
    }

    public function test_cant_search_gifs_without_query_string()
    {
        $response = $this->getJson('/api/gif/search');

        $response->assertStatus(400);
    }

    public function test_dont_found_gift_with_wrong_query()
    {
        $response = $this->getJson('/api/gif/search?query=-');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_get_gif_by_id()
    {
        $searchResponse = $this->getJson('/api/gif/search?query=cats');

        $searchResponse->assertStatus(200)
            ->assertJsonStructure(['data']);

        $gifId = collect($searchResponse->json()['data'][0])->get('id');

        $this->assertNotEmpty($gifId);

        $response = $this->getJson("/api/gif/{$gifId}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_store_new_favorite_with_correct_data()
    {
        $user = User::query()->first();

        $searchResponse = $this->getJson('/api/gif/search?query=cats');

        $searchResponse->assertStatus(200)
            ->assertJsonStructure(['data']);

        $gifId = collect($searchResponse->json()['data'][0])->get('id');

        $this->assertNotEmpty($gifId);

        $response = $this->postJson('/api/gif/favorite/store', [
            "user_id" => $user->id,
            "gif_id" => $gifId,
            "alias" => "Test"
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['payload']);
    }

    public function test_can_update_favorite_with_correct_data()
    {
        $user = User::query()->first();

        $searchResponse = $this->getJson('/api/gif/search?query=cats');

        $searchResponse->assertStatus(200)
            ->assertJsonStructure(['data']);

        $gifId = collect($searchResponse->json()['data'][0])->get('id');

        $this->assertNotEmpty($gifId);

        $this->postJson('/api/gif/favorite/store', [
            "user_id" => $user->id,
            "gif_id" => $gifId,
            "alias" => "Test"
        ]);

        $response = $this->postJson('/api/gif/favorite/store', [
            "user_id" => $user->id,
            "gif_id" => $gifId,
            "alias" => "Test"
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['payload']);
    }

    public function test_cant_store_favorite_with_bad_request()
    {
        $user = User::query()->first();

        $searchResponse = $this->getJson('/api/gif/search?query=cats');

        $searchResponse->assertStatus(200)
            ->assertJsonStructure(['data']);

        $gifId = collect($searchResponse->json()['data'][0])->get('id');

        $this->assertNotEmpty($gifId);

        $response = $this->postJson('/api/gif/favorite/store', [
            "user_id" => $user->id,
            "alias" => "Test"
        ]);

        $response->assertStatus(400);
    }
}
