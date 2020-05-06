<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Post;


class PostControllerTest extends TestCase
{

    use RefreshDatabase; // vamos a modificar datos por ese motivo usaremos esta clase

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201); //OK, creado un recurso
        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function test_validate_title()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);

        // esta bien hecha pero fue imposible completarla
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');//un json que incluye que titulo no esta correcto
    }

    public function test_show()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id"); //id = 1

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); //OK
    }

    public function test_404_show()
    {

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/1000'); //id = 1
        $response->assertStatus(404); //OK
    }

    public function test_update()
    {
        // usar para saber que error esta aconteciendo
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", [
            'title' => 'nuevo'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'nuevo']) //doble comprobacion de lo ingresado
            ->assertStatus(200); //OK

        $this->assertDatabaseHas('posts', ['title' => 'nuevo']);
    }

    public function test_date()
    {
        // usar para saber que error esta aconteciendo
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204); //SIN CONTENIDO

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        // usar para saber que error esta aconteciendo
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        factory(Post::class, 5)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200); //ok

    }

    public function test_guest()
    {

            $this->json('GET',    '/api/posts')->assertStatus(401);
            $this->json('POST',   '/api/posts')->assertStatus(401);
            $this->json('GET',    '/api/posts/1000')->assertStatus(401);
            $this->json('PUT',    '/api/posts/1000')->assertStatus(401);
            $this->json('DELETE', '/api/posts/1000')->assertStatus(401);


    }
}
