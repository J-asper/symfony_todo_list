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
 * @ApiResource(
 *     itemOperations={
 *          "get"={"normalization_context"={"groups"={"todolist_get_item"}}}
 *     }
 * )
 */
class TodoList
{
    /**
     * @var int The id of this TodoList.
     *
     * @Groups({"todolist_get_item"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The description of the list
     *
     * @Groups({"todolist_get_item"})
     * @ORM\Column(type="text", nullable=false)
     */
    public $description;

    /**
     * @var TodoTask[] All tasks for this list
     *
     * @Groups({"todolist_get_item"})
     * @ORM\OrderBy({"state" = "ASC", "id" = "DESC"})
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