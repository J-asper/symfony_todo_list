<?php

namespace App\Entity\Todo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A Todo List consisting of tasks.
 *
 * @ORM\Entity
 * @ApiResource(normalizationContext={"groups"={"todolist"}})
 *
 */
class TodoList
{
    /**
     * @var int The id of this TodoList.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The description of the list
     *
     * @ORM\Column(type="text", nullable=false)
     */
    public $description;

    /**
     * @var TodoTask[] All tasks for this list
     *
     * @Groups({"todolist"})
     * @ORM\OneToMany(targetEntity="TodoTask", mappedBy="list", cascade={"persist", "remove"})
     */
    public $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}