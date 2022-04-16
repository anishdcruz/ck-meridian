<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Organization\Category;
use App\Organization\Organization;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function testCanShowAllOrganization()
    {
        $data = factory(Organization::class, 3)->create();

        $response = $this->get(route('organizations.index'));

        $response->assertStatus(200)
            ->assertJson([
                'collection' => []
            ]);
    }

    //  public function testCanCreateNewOrganization()
    //  {
    //     $data = factory(Organization::class)->create();
    //     $this->post(route('organizations.store'), $data->toArray())
    //         ->assertStatus(201)
    //         ->assertJson($data->toArray());
    // }
}
