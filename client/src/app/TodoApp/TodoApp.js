import React from 'react';
import "./TodoApp.css";
import {TodoList} from "../../components/TodoList/TodoList";
import {TodoPicker} from "../../components/TodoPicker/TodoPicker";
import {addTask, getList, removeTask, updateTask, getListCollection, addList, removeList} from "../../api/Todo/TodoApi";

const defaultAppState = {
    //the list that is currently selected in the dropdown
    pickedListId: TodoPicker.newListId,
    //the list that is currently active.
    activeList: {
        "description": null,
        "id": '',
        "tasks": []
    },
    //a collection of all the lists available in the backend.
    listCollection: {
        "hydra:member": [],
        "hydra:totalItems": 0
    }
};

class TodoApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = defaultAppState;
        this.removeTask = this.removeTask.bind(this);
        this.updateTaskState = this.updateTaskState.bind(this);
        this.addTask = this.addTask.bind(this);
        this.chooseList = this.chooseList.bind(this);
        this.addList = this.addList.bind(this);
        this.removeList = this.removeList.bind(this);
        this.loadListCollectionAndChooseList = this.loadListCollectionAndChooseList.bind(this);
    }

    componentDidMount() {
        this.loadListCollectionAndChooseList();
    }

    addList(description){
        const newList = {
            "description": description,
        };

        return addList({
            description: description
        })
            .then((result) => result.json())
            .then((list) => {
                this.chooseList(list.id);
                this.loadListCollection();
            })
    }

    chooseList(id) {
        if (id === TodoPicker.newListId) {
            this.setState({pickedListId: id});
        } else {
            return getList(id)
                .then((result) => result.json())
                .then((list) => this.setState({
                    activeList: list,
                    pickedListId: list.id,
                }))
                ;
        }
    }

    removeList(id){
        return removeList(id).then(() => {
            this.loadListCollectionAndChooseList();
        });
    }

    loadListCollection() {
        return getListCollection()
            .then((result) => result.json())
            .then((list) => this.setState({
                listCollection: list
            }))
            ;
    }

    loadListCollectionAndChooseList(){
        return this.loadListCollection().then(() => {
            if (this.state.listCollection["hydra:totalItems"] > 0){
                const lists = this.state.listCollection["hydra:member"];
                this.chooseList(lists[lists.length - 1].id);
            }else{
                this.setState({
                    pickedListId: TodoPicker.newListId,
                    activeList: {
                        "description": null,
                        "id": '',
                        "tasks": []
                    },
                });
            }
        });
    }

    addTask(description) {
        const newTask = {
            "description": description,
        };

        this.state.activeList.tasks.unshift(newTask);

        return addTask(this.state.activeList.id, {
            description: description
        })
            .then((result) => result.json())
            .then((task) => {
                newTask.id = task.id;
                this.setState(this.state);
            })
    }

    removeTask(itemIndex) {
        const removedTask = this.state.activeList.tasks.splice(itemIndex, 1);
        this.setState(this.state);
        return removeTask(removedTask[0].id);
    }

    updateTaskState(newState, itemIndex) {
        if (this.state.activeList.tasks[itemIndex].state === newState) return;

        updateTask(this.state.activeList.tasks[itemIndex].id, {
            state: newState
        }).then(() => this.chooseList(this.state.activeList.id));
    }

    render() {
        return (
            <div id="todoapp" className="todoapp">
                <div className="todoapp__list">
                    <TodoList tasks={this.state.activeList.tasks}
                              removeTask={this.removeTask}
                              updateTaskState={this.updateTaskState}
                              addTask={this.addTask}
                              description={this.state.activeList.description}
                    />
                </div>
                <div className="todoapp__picker">
                    <h2>Choose List:</h2>
                    <TodoPicker
                        listCollection={this.state.listCollection}
                        chooseList={this.chooseList}
                        addList={this.addList}
                        removeList={this.removeList}
                        pickedListId={this.state.pickedListId}
                    />
                </div>
            </div>
        );
    }
}

export default TodoApp;

