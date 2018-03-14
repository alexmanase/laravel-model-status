<?php

namespace Spatie\ModelStatus\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\ModelStatus\Events\StatusUpdated;
use Spatie\ModelStatus\Tests\Models\TestModel;
use Spatie\ModelStatus\Tests\TestCase;

class StatusUpdatedTest extends TestCase
{
    /** @var \Spatie\ModelStatus\Tests\Models\TestModel */
    protected $testModel;

    protected function setUp()
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'name' => 'name',
        ]);
    }

    /** @test */
    public function it_fires_an_event_when_status_changes(): void
    {
        $this->testModel->setStatus('pending', 'waiting on action');

        Event::fake();

        $this->testModel->setStatus('status a', 'Reason a');

        Event::assertDispatched(StatusUpdated::class,
            function (StatusUpdated $event) {
                if ($event->model->id !== $this->testModel->id) {
                    return false;
                }

                if ($event->newStatus->name !== 'status a') {
                    return false;
                }

                if ($event->oldStatus->name !== 'pending') {
                    return false;
                }

                return true;
            });
    }

    /** @test */
    public function it_does_not_fire_an_event_when_status_stays_the_same(): void
    {
        $this->testModel->setStatus('pending', 'waiting on action');
        Event::fake();

        $this->testModel->setStatus('pending', 'Still waiting');

        Event::assertNotDispatched(StatusUpdated::class);
    }
}
