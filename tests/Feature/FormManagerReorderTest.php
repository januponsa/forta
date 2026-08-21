<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\FormManager;
use App\Models\User;
use App\Models\Form;
use App\Models\ActivityType;

class FormManagerReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_form_manager()
    {
        $admin = User::create([
            'name' => 'Admin', 
            'email' => 'admin_prodi_test@test.com', 
            'password' => bcrypt('password'), 
            'role' => 'superadmin',
            'is_active' => true
        ]);

        $this->actingAs($admin)
            ->get(route('admin.forms'))
            ->assertStatus(200);
    }

    public function test_search_and_filter_pagination()
    {
        $admin = User::create([
            'name' => 'Admin', 
            'email' => 'admin_test2@test.com', 
            'password' => bcrypt('password'), 
            'role' => 'superadmin',
            'is_active' => true
        ]);
        
        $type = ActivityType::create(['name' => 'KKN', 'description' => 'KKN', 'slug' => 'kkn']);
        
        Form::create(['title' => 'Form 1', 'slug' => 'f1', 'phase' => 'A', 'status' => 'draft', 'semester' => 'Ganjil 2026', 'activity_type_id' => $type->id]);
        Form::create(['title' => 'Form 2', 'slug' => 'f2', 'phase' => 'B', 'status' => 'draft', 'semester' => 'Genap 2026', 'activity_type_id' => $type->id]);

        Livewire::actingAs($admin)
            ->test(FormManager::class)
            ->set('semesterFilter', 'Ganjil 2026')
            ->assertSee('Form 1')
            ->assertDontSee('Form 2')
            ->set('search', 'Form 1')
            ->assertSee('Form 1');
    }

    public function test_reorder_forms()
    {
        $admin = User::create([
            'name' => 'Admin', 
            'email' => 'admin_test3@test.com', 
            'password' => bcrypt('password'), 
            'role' => 'superadmin',
            'is_active' => true
        ]);
        
        $type = ActivityType::create(['name' => 'Magang', 'description' => 'Magang', 'slug' => 'magang']);
        
        $form1 = Form::create(['title' => 'F1', 'slug' => 'f11', 'phase' => 'A', 'status' => 'draft', 'semester' => 'Gasal', 'display_order' => 1, 'activity_type_id' => $type->id]);
        $form2 = Form::create(['title' => 'F2', 'slug' => 'f12', 'phase' => 'A', 'status' => 'draft', 'semester' => 'Gasal', 'display_order' => 2, 'activity_type_id' => $type->id]);
        $form3 = Form::create(['title' => 'F3', 'slug' => 'f13', 'phase' => 'A', 'status' => 'draft', 'semester' => 'Gasal', 'display_order' => 3, 'activity_type_id' => $type->id]);

        Livewire::actingAs($admin)
            ->test(FormManager::class)
            ->set('semesterFilter', 'Gasal')
            ->call('enableReorderMode')
            ->call('reorder', [$form3->id, $form2->id, $form1->id])
            ->assertHasNoErrors();

        $this->assertEquals(1, $form3->fresh()->display_order);
        $this->assertEquals(2, $form2->fresh()->display_order);
        $this->assertEquals(3, $form1->fresh()->display_order);
    }
}
