import React from 'react';
import "./TodoTask.css";

const bemClass = 'todotask';

export class TodoTask extends React.Component {
    constructor(props) {
        super(props);
        this.onCloseClick = this.onCloseClick.bind(this);
        this.onStateClick = this.onStateClick.bind(this);
    }

    onCloseClick() {
        this.props.removeTask(parseInt(this.props.index));
    }

    onStateClick(newState) {
        this.props.updateTaskState(newState, parseInt(this.props.index));
    }

    render() {
        const state = this.props.item.state;

        return (
            <li className={`${makeBemClass(state)} clearfix`}>
                <div className={`${bemClass}__buttons`}>
                    <button type="button" className={makeStateButtonClass(TodoTask.state.CREATED, state)} onClick={() => this.onStateClick(TodoTask.state.CREATED)}>Created</button>
                    <button type="button" className={makeStateButtonClass(TodoTask.state.IN_PROGRESS, state)} onClick={() => this.onStateClick(TodoTask.state.IN_PROGRESS)}>In Progress</button>
                    <button type="button" className={makeStateButtonClass(TodoTask.state.DONE, state)} onClick={() => this.onStateClick(TodoTask.state.DONE)}>Done</button>
                </div>
                <div className={`${bemClass}__description`}>
                    {this.props.item.description}
                </div>
                <button type="button" className={`${bemClass}__close`} onClick={this.onCloseClick}>&times;</button>
            </li>
        );
    }
}

TodoTask.state = {
    CREATED: 0,
    IN_PROGRESS: 1,
    DONE: 2
};

TodoTask.stateClassNames = {
    [TodoTask.state.CREATED] : 'created',
    [TodoTask.state.IN_PROGRESS] : 'inprogress',
    [TodoTask.state.DONE] : 'done',
};

function makeBemClass(state){
    return `${bemClass} ${bemClass}--state-${TodoTask.stateClassNames[state]}`;
}

function makeStateIconClass(state){
    return (state === TodoTask.state.CREATED ? 'glyphicon-ok' : (state === TodoTask.state.IN_PROGRESS ? 'glyphicon-ok' : 'glyphicon-ok'));
}

function makeStateButtonClass(state, activeState){
    let buttonClass = `${bemClass}__state`;
    if (state === activeState) buttonClass += ` ${buttonClass}--active`;
    return buttonClass;
}