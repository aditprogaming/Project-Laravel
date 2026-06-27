<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortivaTemplateTest extends TestCase
{
    public function test_admin_can_add_a_new_template(): void
    {
        $response = $this->withSession(['admin' => true])
            ->post('/template', ['name' => 'Template Baru']);

        $response->assertRedirect('/template');
        $this->assertCount(4, session('admin_templates'));
        $this->assertSame('Template Baru', session('admin_templates.3.name'));
    }

    public function test_admin_can_edit_an_existing_template(): void
    {
        $response = $this->withSession([
            'admin' => true,
            'admin_templates' => [
                ['id' => 1, 'name' => 'Model 1'],
                ['id' => 2, 'name' => 'Model 2'],
                ['id' => 3, 'name' => 'Model 3'],
            ],
        ])->patch('/template/2', ['name' => 'Model 2 Edit']);

        $response->assertRedirect('/template');
        $this->assertSame('Model 2 Edit', session('admin_templates.1.name'));
    }
}
