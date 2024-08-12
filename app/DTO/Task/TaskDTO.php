<?php

namespace App\DTO\Task;

class TaskDTO
{
    public ?string $title;
    public ?string $description;
    public ?\DateTime $due_date;
    public ?string $status;

    public function __construct(
        $title = null, 
        $description = null, 
        $due_date = null, 
        $status = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->due_date = $due_date;
        $this->status = $status;
    }
}
