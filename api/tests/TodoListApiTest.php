<?php

namespace App\Tests;

use App\Entity\Todo\TodoList;
use Doctrine\Common\Collections\ArrayCollection;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class TodoListApiTest extends WebTestCase
{
    //Including this trait will make sure database is reload from the fixtures before every test
    use RefreshDatabaseTrait;

    /**
     * Test Retrieves the to-do lists
     */
    public function testGetAllTodoLists(): void
    {
        $response = $this->request('GET', '/todo_lists');
        $json = json_decode($response->getContent(), true);


        //assert there is a valid reply
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert that we have 1 TO-DO list
        $this->assertArrayHasKey('hydra:totalItems', $json);
        $this->assertEquals(1, $json['hydra:totalItems']);

        $this->assertArrayHasKey('hydra:member', $json);
        $this->assertCount(1, $json['hydra:member']);

        //Assert that the type of the first member is TodoList, and also check the description
        $this->assertSame($json['hydra:member'][0]['@type'], 'TodoList');
        $this->assertSame($json['hydra:member'][0]['description'], 'Get New Job');
    }

    /**
     * Test get a specific to-do list from the API
     */
    public function testGetTodoList(): void
    {
        $response = $this->request('GET', $this->findOneIriBy(TodoList::class, ['description' => 'Get New Job']));
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert that description of the returned JSON object is correct
        $this->assertEquals('Get New Job', $json['description']);

        //assert that the retrieved list has 4 to-do tasks:
        $this->assertCount(4, $json['tasks']);

        //assert that the returned tasks are properly serialized according to their normalization context (todolist_get_item)
        $firstTask = $json['tasks'][0];
        $this->assertArrayHasKey('@id', $firstTask);
        $this->assertArrayHasKey('@type', $firstTask);
        $this->assertArrayHasKey('id', $firstTask);
        $this->assertArrayHasKey('state', $firstTask);
        $this->assertArrayHasKey('description', $firstTask);
        $this->assertArrayHasKey('createDate', $firstTask);
    }

    /**
     * Test post new to-do List
     */
    public function testPostTodoList(): void
    {
        $data = [
            'description' => 'List made by Functional Test'
        ];

        $response = $this->request('POST', '/todo_lists', $data);
        $json = json_decode($response->getContent(), true);

        //assert there is a valid reply, 201 created code
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //Assert returned response has a JSON object with property description that has the right value
        $this->assertSame($json['description'], $data['description']);

        //check that we have 2 todolists now in the backend:
        $lists = $this->getAllLists();
        $this->assertEquals(2, count($lists));

        //Assert new entity has the right description:
        $this->assertEquals($lists[1]->description, $data['description']);
    }

    /**
     * Test check post blocks new to-do list if required fields are not present.
     */
    public function testPostTodoListRequiredFields(): void
    {
        $data = [
        ];

        $response = $this->request('POST', '/todo_lists', $data);
        $json = json_decode($response->getContent(), true);

        //assert bad request response from API
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        //assert there is a single violation (description is required)
        $this->assertArrayHasKey('violations', $json);
        $this->assertCount(1, $json['violations']);

        //assert that the violation is for the description field
        $this->assertArrayHasKey('propertyPath', $json['violations'][0]);
        $this->assertEquals('description', $json['violations'][0]['propertyPath']);
    }

    /**
     * Test delete To-do List
     */
    public function testDeleteTodoList(): void
    {
        $lists = $this->getAllLists();
        $listCount = count($lists);
        $firstListId = $lists[0]->getId();

        $response = $this->request('DELETE', "/todo_lists/{$firstListId}");

        //assert there is a valid reply, 204 No Content header
        $this->assertEquals(204, $response->getStatusCode());

        //assert that we have one less todolist now:
        $this->assertEquals($listCount - 1, count($this->getAllLists()));
    }

    /**
     * @return ArrayCollection returns all TodoLists inside the database
     */
    protected function getAllLists(){
        return static::$container->get('doctrine')->getRepository(TodoList::class)->findAll();
    }
}