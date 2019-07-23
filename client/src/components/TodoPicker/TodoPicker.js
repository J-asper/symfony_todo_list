import React from 'react';
import "./TodoPicker.css";

const bemClass = 'todopicker';

export class TodoPicker extends React.Component {
    constructor(props) {
        super(props);
        this.onChooseListChange = this.onChooseListChange.bind(this);
        this.onNewListChange = this.onNewListChange.bind(this);
        this.onNewListClick = this.onNewListClick.bind(this);
        this.onDeleteListClick = this.onDeleteListClick.bind(this);

        this.state = {
            newList : '',
        };
    }

    onChooseListChange(evt) {
        const val = evt.target.value.toString();
        if (this.props.chooseList) this.props.chooseList(val);
    }

    onNewListChange(evt){
        this.setState({newList: evt.target.value});
    }

    onNewListClick(evt){
        evt.preventDefault();
        if (!this.state.newList) return;

        if (this.props.addList) this.props.addList(this.state.newList);
        this.setState({newList : ''});
    }

    onDeleteListClick(evt){
        if (this.props.removeList) this.props.removeList(this.props.pickedListId);
    }

    render() {
        return (
            <div className={bemClass}>
                <div className={`${bemClass}__chooselist`} >
                    <select onChange={this.onChooseListChange} value={this.props.pickedListId }>
                        {this.props.listCollection["hydra:member"].map((item) => {
                            return (
                                <option key={item.id} value={item.id}>{item.description}</option>
                            );
                        })}
                        <option key={TodoPicker.newListId} value={TodoPicker.newListId}>Add New List</option>
                    </select>
                    {this.props.pickedListId !== TodoPicker.newListId && <button onClick={this.onDeleteListClick}>Delete</button>}
                </div>

                {this.props.pickedListId === TodoPicker.newListId &&
                <div className={`${bemClass}__addlist`}>
                    <p>Add List:</p>
                    <form>
                        <input type="text" value={this.state.newList} onChange={this.onNewListChange} autoFocus />
                        <button onClick={this.onNewListClick}>Add List</button>
                    </form>
                </div>

                }
            </div>
        );
    }
}

TodoPicker.newListId = "0";