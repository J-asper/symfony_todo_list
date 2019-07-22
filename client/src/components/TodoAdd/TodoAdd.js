import React from 'react';
import "./TodoAdd.css";

const bemClass = 'todoadd';

export class TodoAdd extends React.Component {
    constructor(props) {
        super(props);
        this.onClick = this.onClick.bind(this);
        this.state = {description : ""};
    }

    handleChange(evt) {
        this.setState({description: evt.target.value})
    }

    onClick(evt) {
        evt.preventDefault();
        if (this.state.description){
            if (this.props.addTask) this.props.addTask(this.state.description);
            this.setState({description : ""});
        }
    }

    render() {
        return (
            <form className={bemClass}>
                <input type="text" value={this.state.description} onChange={this.handleChange.bind(this)} />
                <button onClick={this.onClick}>Add</button>
            </form>
        );
    }
}