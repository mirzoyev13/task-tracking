<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    public function test_task_create()
    {
        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => '2024-12-31',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'status' => 'pending',
                    'due_date' => '2024-12-31',
                ]
            ]);
    }

    public function test_task_retrieval()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'due_date' => $task->due_date,
                ]
            ]);
    }

    public function test_task_update()
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'completed',
            'due_date' => '2024-12-31',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Task',
                    'description' => 'Updated Description',
                    'status' => 'completed',
                    'due_date' => '2024-12-31',
                ]
            ]);
    }

    public function test_task_delete()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_filter_due_date()
    {
        Task::factory()->create(['due_date' => '2024-12-31']);

        $response = $this->getJson('/api/v1/tasks?due_date=2024-12-31');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['due_date' => '2024-12-31']
                ]
            ]);

    }

    public function test_task_filter_status()
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'completed']);

        $response = $this->getJson('/api/v1/tasks?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['status' => 'pending']
                ]
            ]);
    }
}
