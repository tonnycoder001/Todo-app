<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    // validation
    #[Rule('required|min:2|max:255')]
    public $name;

    // search
    public $search;

    // editing todo
    public $editingTodoID;

    // validation rule when editing todo
    #[Rule('required|min:2|max:255')]
    public $editingTodoName;

    // creating a new to do
    public function create()
    {
        // validate
        $validated =  $this->validateOnly('name');

        // create todo
        Todo::create($validated);

        // clear input
        $this->reset('name');

        // flash message
        session()->flash('success', 'Todo created successfully');

        // reset page
        $this->resetPage();
    }

    // delete todo
    public function delete(Todo $todo)
    {
        // delete todo
        $todo->delete();
    }

    // showing if a todo is completed or not
    public function toggle(Todo $todo)
    {
        // toggle completed
        $todo->update([
            'completed' => !$todo->completed
        ]);
    }

    // editing a todo
    public function edit($todoId)
    {
        $this->editingTodoID = $todoId;
        $this->editingTodoName = Todo::find($todoId)->name;
    }

    // cancel edit
    public function cancelEdit()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    // update todo
    public function update()
    {
        // validate the newly created name
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName
        ]);
        // cancel edit while editing
        $this->cancelEdit();
    }

    public function render()
    {
        // list all todos, paginate and search.
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(4)
        ]);
    }
}
