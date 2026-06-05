<?php

namespace Tests\Feature\Web;

use App\Models\WorkshopPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WorkshopTest extends TestCase
{
    use RefreshDatabase;

    public function test_workshop_page_is_accessible_to_guests(): void
    {
        $this->get(route('workshop'))
            ->assertOk();
    }

    public function test_workshop_page_passes_photos_to_inertia(): void
    {
        WorkshopPhoto::create(['filename' => 'a.jpg', 'original_name' => 'a.jpg']);

        $this->get(route('workshop'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Workshop')
                ->has('photos', 1)
            );
    }

    public function test_workshop_page_passes_empty_photos(): void
    {
        $this->get(route('workshop'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Workshop')
                ->has('photos', 0)
            );
    }
}
