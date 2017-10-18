@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h2>Listagem de Categorias</h2>
        </div>
        <div class="row">
            {!! Table::withContents($categories->items())->striped()
             ->callback('Ações', function($campo, $model){
                $linkEdit = route('admin.categories.edit', ['category' => $model->id]);
                $linkShow = route('admin.categories.show', ['category' => $model->id]);
                return Button::link('Editar')->asLinkTo($linkEdit).' | '.Button::link('Ver')->asLinkTo($linkShow);
             }) !!}
        </div>
        {!! $categories->links() !!}
        {!! Button::primary('Nova Categoria')->asLinkTo(route('admin.categories.create')) !!}
    </div>
@endsection