<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;
    public $search;


    public $editingTodoId;
    #[Rule('required|min:3|max:50')]
    public $editingTodoName;


    public function create()
    {
        $validated = $this->validateOnly('name');
        Todo::create($validated);
        $this->reset('name');
        session()->flash('success' , 'created successfully');
        $this->resetPage();
    }

    public function delete(Todo $todo)
    {
        $todo->delete();
    }

    public function mark(Todo $todo)
    {
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit(Todo $todo)
    {
        $this->editingTodoId = $todo->id;
        $this->editingTodoName = $todo->name;
    }

    public function update()
    {
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoId)->update([
           'name' => $this->editingTodoName
        ]);
        $this->cancelEdit();

    }

    public function cancelEdit()
    {
        $this->reset('editingTodoId' , 'editingTodoName');
    }
    public function render()
    {
        $todos = Todo::latest()->where('name' , 'like' , "%{$this->search}%")->paginate(6);
        return view('livewire.todo-list' , compact('todos'));
    }
}
