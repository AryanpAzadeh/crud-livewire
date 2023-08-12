<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use mysql_xdevapi\Exception;

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

    public function delete($todoId)
    {
        try {
           $todo = Todo::findOrFail($todoId)->delete();
        }catch (\Exception $ex){
            session()->flash('error' , 'Failed to Delete !');
        }
    }

    public function mark($todoId)
    {
        try {
            $todo = Todo::findOrFail($todoId);
            $todo->completed = !$todo->completed;
            $todo->save();
        }catch (\Exception $ex){
            session()->flash('error' , 'Failed to Mark !');
        }

    }

    public function edit($todoId)
    {
        try {
            $this->editingTodoId = Todo::findOrFail($todoId)->id;
            $this->editingTodoName = Todo::findOrFail($todoId)->name;
        }catch (\Exception $ex)
        {
            session()->flash('error' , 'Failed to Find Todo !');
        }

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
