<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        // usar para saber que error esta aconteciendo $this->withoutExceptionHandling();

        $response = $this->json('POST', 'api/posts',[
            'title' => 'post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'post de prueba']) //doble comprobacion de lo ingresado
            ->assertStatus(201); //OK crea el recurso

        $this->assertDatabaseHas('posts', ['title' => 'post de prueba']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST', 'api/posts',[
            'title' => ''
        ]);

        // esta bien hecha pero fue imposible completarla
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');//un json que incluye que titulo no esta correcto
    }
}
