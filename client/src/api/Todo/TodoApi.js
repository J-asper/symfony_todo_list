import { TodoTask } from "../../components/TodoTask/TodoTask";
import { fetch } from "../../utils/dataAccess";

export const resource = {
    TODO_LISTS : '/todo_lists',
    TODO_TASKS : '/todo_tasks'
};

export function getList(listId, options){
    return fetch(resource.TODO_LISTS + `/${listId}`, options || {})
}

export function getListCollection(options){
    return fetch(resource.TODO_LISTS, options || {})
}

export function removeList(listId, options){
    return fetch(resource.TODO_LISTS + `/${listId}`, Object.assign({
        method: 'DELETE'
    }, options || {}))
}

export function addTask(listId, data, options){
    data = Object.assign({
        description : 'New Task',
        state: TodoTask.state.CREATED,
        list: `${resource.TODO_LISTS}/${listId}`
    }, data || {});

    return fetch(resource.TODO_TASKS, Object.assign({
        method: 'POST',
        body: JSON.stringify(data)
    }, options || {}))
}

export function addList(data, options){
    data = Object.assign({
        description : 'New List',
    }, data || {});

    return fetch(resource.TODO_LISTS, Object.assign({
        method: 'POST',
        body: JSON.stringify(data)
    }, options || {}))
}

export function updateTask(taskId, data, options){
    return fetch(`${resource.TODO_TASKS}/${taskId}`, Object.assign({
        method: 'PUT',
        body: JSON.stringify(data || {})
    }, options || {}))
}

export function removeTask(taskId, options){
    return fetch(resource.TODO_TASKS + `/${taskId}`, Object.assign({
        method: 'DELETE'
    }, options || {}))
}