<?php

namespace App\Tests;

use App\Entity\Todo\TodoList;
use App\Entity\Todo\TodoTask;
use Doctrine\Common\Collections\ArrayCollection;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class TodoTaskApiTest extends WebTestCase
{
    //Including this trait will make sure database is reload from the fixtures before every test
    use RefreshDatabaseTrait;

    /**
     * Test Retrieves the to-do tasks
     */
    public function testGetAllTodoTasks(): void
    {
        $response = $this->request('GET', '/todo_tasks');
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert that we have 4 TO-DO tasks
        $this->assertArrayHasKey('hydra:totalItems', $json);
        $this->assertEquals(4, $json['hydra:totalItems']);

        $this->assertArrayHasKey('hydra:member', $json);
        $this->assertCount(4, $json['hydra:member']);

        //Assert that the type of the first member is a TodoTask, and also check the description
        $this->assertSame($json['hydra:member'][0]['@type'], 'TodoTask');
        $this->assertSame($json['hydra:member'][0]['description'], 'Apply for new job');
    }

    /**
     * Test get a specific to-do task from the API
     */
    public function testGetTodoTask(): void
    {
        $response = $this->request('GET', $this->findOneIriBy(TodoTask::class, ['description' => 'Develop TODO test']));
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert that description of the returned JSON object is correct
        $this->assertEquals('Develop TODO test', $json['description']);

        //assert that the returned task is properly serialized
        $this->assertArrayHasKey('@id', $json);
        $this->assertArrayHasKey('@type', $json);
        $this->assertArrayHasKey('@context', $json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('state', $json);
        $this->assertArrayHasKey('description', $json);
        $this->assertArrayHasKey('createDate', $json);
        $this->assertArrayHasKey('list', $json);
    }

    /**
     * Test post new To-do Task
     */
    public function testPostTodoTask(): void
    {
        $data = [
            'description' => 'Task Made By Functional Test',
            'state' => TodoTask::STATE_IN_PROGRESS,
            'list' => $this->findOneIriBy(TodoList::class, ['description' => 'Get New Job'])
        ];

        $response = $this->request('POST', '/todo_tasks', $data);
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply, 201 created code
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //Assert returned response has a JSON object with property description that has the right value
        $this->assertSame($json['description'], $data['description']);

        //check that we have 5 tasks now in the backend:
        $tasks = $this->getAllTasks();
        $this->assertEquals(5, count($tasks));

        //Assert new entity has the right description:
        $this->assertEquals($tasks[4]->description, $data['description']);

        $getNewJobList = static::$container->get('doctrine')->getRepository(TodoList::class)->findOneBy(['description' => 'Get New Job']);

        //Assert that the 'Get New Job' list now has 5 tasks:
        $this->assertCount(5, $getNewJobList->tasks);

        //Assert that the 'Get New Job' list now has a task with the description 'Task Made By Functional Test'
        $tasksMadeByTest = array_values(array_filter($getNewJobList->tasks->toArray(), function($task) use ($data){
           return $task->description === $data['description'];
        }));

        $this->assertCount(1, $tasksMadeByTest);

        //assert that this task has the state 'In Progress'
        $this->assertEquals(TodoTask::STATE_IN_PROGRESS, $tasksMadeByTest[0]->state);
    }

    /**
     * Test check post blocks new to-do task if required fields are not present.
     */
    public function testPostTodoTaskRequired(): void
    {
        $data = [
        ];

        $response = $this->request('POST', '/todo_tasks', $data);
        $json = json_decode($response->getContent(), true);

        //assert bad request response from API
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert there are 2 violations (list and description are required)
        $this->assertArrayHasKey('violations', $json);
        $this->assertCount(2, $json['violations']);

        //assert that the violations are for the list and description fields
        $this->assertArrayHasKey('propertyPath', $json['violations'][0]);
        $this->assertEquals('description', $json['violations'][0]['propertyPath']);
        $this->assertArrayHasKey('propertyPath', $json['violations'][1]);
        $this->assertEquals('list', $json['violations'][1]['propertyPath']);
    }

    /**
     * Test delete to-do task
     */
    public function testDeleteTodoTask(): void
    {
        $tasks = $this->getAllTasks();
        $taskCount = count($tasks);
        $firstTaskId = $tasks[0]->getId();

        $response = $this->request('DELETE', "/todo_tasks/{$firstTaskId}");

        //assert there is a valid reply, 204 No Content header
        $this->assertEquals(204, $response->getStatusCode());

        //assert that we have one less task now:
        $this->assertEquals($taskCount - 1, count($this->getAllTasks()));
    }

    /**
     * Test update To-do Task
     */
    public function testUpdateTodoTask(): void
    {
        $data = [
            'description' => 'Finish TODO test'
        ];

        $response = $this->request('PUT', $this->findOneIriBy(TodoTask::class, ['description' => 'Develop TODO test']), $data);
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert reply JSON object has the new description:
        $this->assertEquals('Finish TODO test', $json['description']);

        //assert that we no longer have a task with the description 'Develop TO DO test'
        $this->assertEquals(null, static::$container->get('doctrine')->getRepository(TodoTask::class)->findOneBy(['description' => 'Develop TODO test']));

        //assert that we now instead have a task with the description 'Finish TO DO test'
        $this->assertNotEquals(null, static::$container->get('doctrine')->getRepository(TodoTask::class)->findOneBy(['description' => 'Finish TODO test']));
    }

    /**
     * @return ArrayCollection returns all tasks inside the database
     */
    protected function getAllTasks(){
        return static::$container->get('doctrine')->getRepository(TodoTask::class)->findAll();
    }
}