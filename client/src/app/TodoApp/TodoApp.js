import React from 'react';
import "./TodoApp.css";
import {TodoList} from "../../components/TodoList/TodoList";
import {TodoTask} from "../../components/TodoTask/TodoTask";
import {addTask, getList, removeTask, updateTask} from "../../api/Todo/TodoApi";

const defaultAppState = {
    list: {
        "@context": "/contexts/TodoList",
        "@id": "/todo_lists/1",
        "@type": "TodoList",
        "description": null,
        "tasks": []
    }
};

class TodoApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = defaultAppState;
        this.removeTask = this.removeTask.bind(this);
        this.updateTaskState = this.updateTaskState.bind(this);
        this.addTask = this.addTask.bind(this);
    }

    componentDidMount() {
        this.loadList(1);
    }

    loadList(id) {
        getList(id)
            .then((result) => result.json())
            .then((list) => this.setState({
                list: list
            }))
        ;
    }

    addTask(description) {
        const newTask = {
            "state": TodoTask.state.CREATED,
            "description": description,
            "createDate": new Date()
        };

        this.state.list.tasks.unshift(newTask);

        addTask(this.state.list.id, {
            description: description
        })
            .then((result) => result.json())
            .then((task) => {
                newTask.id = task.id;
                this.setState(this.state);
            })
    }

    removeTask(itemIndex) {
        const removedTask = this.state.list.tasks.splice(itemIndex, 1);
        this.setState(this.state);
        if (removedTask) removeTask(removedTask[0].id);
    }

    updateTaskState(newState, itemIndex) {
        if (this.state.list.tasks[itemIndex].state === newState) return;

        this.state.list.tasks[itemIndex].state = newState;
        this.setState(this.state);
        updateTask(this.state.list.tasks[itemIndex].id, {
            state: newState
        }).then(() => this.loadList(this.state.list.id));
    }

    render() {
        return (
            <div id="todoapp">
                <TodoList tasks={this.state.list.tasks}
                          removeTask={this.removeTask}
                          updateTaskState={this.updateTaskState}
                          addTask={this.addTask}
                />
            </div>
        );
    }
}

export default TodoApp;

