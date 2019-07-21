<?php

namespace App\Entity\Todo;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A single TODO task.
 *
 * @ORM\Entity
 * @ApiResource
 *
 */
class TodoTask
{
    CONST STATE_CREATED = 0;
    CONST STATE_IN_PROGRESS = 1;
    CONST STATE_DONE = 2;

    /**
     * @var int The id of this review.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("todolist")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int the state of the current task
     *
     * @Groups("todolist")
     * @ORM\Column(type="integer", nullable=false)
     */
    public $state;

    /**
     * @var string The description of the task
     *
     * @Groups("todolist")
     * @ORM\Column(type="text", nullable=false)
     */
    public $description;

    /**
     * @var \DateTimeInterface Creation date of the task
     *
     * @Groups("todolist")
     * @ORM\Column(type="datetime")
     */
    public $createDate;

    /**
     * @var TodoList The list to which this task belongs
     *
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="tasks")
     */
    public $list;

    /**
     * TodoTask constructor.
     */
    public function __construct()
    {
        $this->state = $this::STATE_CREATED;
        $this->createDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}