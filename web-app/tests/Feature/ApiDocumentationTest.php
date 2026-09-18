<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
  public function test_it_documents_api_controller_routes(): void
  {
    $response = $this->getJson('/api-docs.json');

    $response->assertOk()
      ->assertJsonPath('openapi', '3.0.3')
      ->assertJsonPath('paths./api/vegetation.get.operationId', 'api.vegetation.index')
      ->assertJsonPath('paths./api/vegetation.get.summary', 'Return all (non-deleted) vegetation as a JSON array suitable for the Digital Twin map-o.')
      ->assertJsonPath('paths./api/vegetation.get.parameters.0.name', 'status[]')
      ->assertJsonPath('paths./api/vegetation.get.parameters.0.in', 'query')
      ->assertJsonPath('paths./api/vegetation.get.parameters.0.schema.type', 'array')
      ->assertJsonPath('paths./api/vegetation.get.parameters.0.schema.items.description', 'Must match a value in vegetation_status.name.');
  }
}