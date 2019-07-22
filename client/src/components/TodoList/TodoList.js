import React from 'react';
import {TodoTask} from "../TodoTask/TodoTask";
import {TodoAdd} from "../TodoAdd/TodoAdd";

import "./TodoList.css";
import FlipMove from 'react-flip-move';

const bemClass = 'todolist';
let key=0;

export class TodoList extends React.Component {
    render() {
        const tasks = this.props.tasks.map((item, index) => {
            return (
                <TodoTask key={generatekey(item)} item={item} index={index}
                          removeTask={this.props.removeTask}
                          updateTaskState={this.props.updateTaskState}
                />
            );
        });

        return (
            <div className={bemClass}>
                <ul>
                    <FlipMove>
                        {tasks}
                    </FlipMove>
                </ul>
                <TodoAdd
                    addTask={this.props.addTask}
                />
            </div>
        );
    }
}

function generatekey(item){
    if (item){
        if (typeof item.id === 'undefined') item.id = -(key++);
        return item.id;
    }
    return 0;
}