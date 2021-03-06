@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h2>PÁGINA TESTE {!! \Bootstrapper\Facades\Button::primary('Nova Mesa')->withAttributes(['data-toggle' => 'modal', 'data-target' => '#novaMesaModal']) !!}</h2>
    </div>
        <div class="row">
            <div class="col-xs-7 col-sm-6 col-lg-8" style="margin-left:-60px; border-color: #2F3133; border: groove; height: 450px;" id="tabsCategorias" data-url="<?= route('admin.categories.create') ?>">
                @php
                    foreach($categories as $category){
                        $brands = App\Models\Brand::all()->where('category_id', '=', $category->id);
                        $listadivs = [];
                        foreach ($brands as $brand){
                            $exibe = App\Models\Brand::criaLista($brand->id);

                            array_push($listadivs, $exibe);
                        }

                        $string = implode($listadivs);

                       $names[] = [
                                'title' => $category->name,
                                'content' => "<div>$string</div>"
                            ];
                            unset($listadivs);
                     }
                      $names[] = [
                         'title' => Icon::create('plus'),
                         'content' => ''
                     ];
                @endphp
                {!! Tabbable::withContents($names) !!}
            </div>
            <div class="col-xs-5 col-sm-6 col-lg-5" style="margin-right:-40px; border-color: #2F3133; border: groove; height: 450px; overflow: auto">
                @if(isset($order))
                        <div align="center" style="background-color:#99cb84;"> Produtos de {{$order->client->name}}</div>
                        {!! $tabela = App\Models\Sell::atualizaTabelaDeItens($order->id)!!}

                    @else
                        <div align="center" style="background-color:#99cb84;"> Lista de Produtos </div>
                @endif

            </div>
        </div>
        <div style="margin-left:-70px">Mesas:</div>
        <div class="col-xs-7 col-sm-6 col-lg-7" style="max-height: 70px; min-width:770px; margin-left:-80px; overflow-x: auto;white-space: nowrap;">
            @php
                $orderController = new App\Http\Controllers\OrderController();
                echo $orderController->carregaPedidosAbertos();
            @endphp
        </div>
        <div class="col-xs-5 col-sm-6 col-lg-5" style="margin-top:-20px; margin-right:-150px; text-align:left;">
            Valor total da compra: R$@if(isset($order)){{number_format((float)$order->total, 2, '.', '')}} @else 0,00 @endif <br>
            @php
                if(isset($order)){
                    echo Button::success('Concluir Venda')->addAttributes(['style' => 'margin-left:25px;height:40px; width:210px', 'data-toggle' => 'modal', 'data-target' => '#concluirVendaModal']);
                    echo Button::danger('Cancelar Venda')->addAttributes(['style' => 'margin-right:-25px;margin-left:25px; height:40px; width:210px', 'data-toggle' => 'modal', 'data-target' => '#cancelarVendaModal']);
                }else{
                    echo Button::success('Concluir Venda')->addAttributes(['style' => 'margin-left:25px;height:40px; width:210px', 'disabled' => 'true']);
                    echo Button::danger('Cancelar Venda')->addAttributes(['style' => 'margin-right:-25px;margin-left:25px;height:40px; width:210px', 'disabled' => 'true']);
                }
            @endphp
        </div>
    </div>

    <div data-keyboard="false" data-backdrop="static" class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title titulo" id="titulo"></h4>
                </div>
                {!! Form::open(array('action' => 'SellController@addProducts', 'method' => 'post')) !!}
                <div class="modal-body task" id="task" >
                </div>
                <div class="modal-footer">
                    @php
                        if(isset($order))
                            echo Form::hidden('order_id', $order->id);
                    @endphp
                    {!! Form::submit('Adicionar à venda!') !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div data-keyboard="false" data-backdrop="static" class="modal fade" id="novaMesaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title titulo" id="titulo">Nova Mesa</h4>
                </div>
                {!! Form::open(array('action' => 'SellController@criarMesa', 'method' => 'post')) !!}

                <div class="modal-body task" id="task" >
                    <div class="form-group">
                        {!! Form::Label('cliente', 'Selecione um Cliente:') !!}
                        <select class="selectpicker" data-live-search="true" name="item_id">
                            {!! $clientes = App\Models\Client::all() !!}
                            @foreach($clientes as $client)
                                <option value="{{$client->id}}">{{$client->nickname}}</option>
                            @endforeach
                        </select>
                        {!! Form::hidden('associated', 0) !!}
                        {!! Form::checkbox('associated', 1) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    @php
                    if(isset($order))
                        echo Form::hidden('order_id', $order->id);
                    @endphp

                    {!! Form::submit('Criar Mesa!', array('class' => 'btn btn-primary')) !!}
                    {!! Form::close() !!}
                    {!! Button::primary('Novo Cliente')->asLinkTo(route('admin.clients.create')) !!}
                </div>
            </div>
        </div>
    </div>

    <div data-keyboard="false" data-backdrop="static" class="modal fade" id="concluirVendaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Finalizar Venda</h4>
                </div>
                {!! Form::open(array('action' => 'SellController@concluirVenda', 'method' => 'post')) !!}
                <div class="modal-body">
                    Selecione a forma de pagamento: <br>
                    {!! Form::select('formaPagamento', ['Dinheiro', 'Cartão de Débito', 'Cartão de Crédito'])  !!}

                    @php
                        if(isset($order)){
                            echo Form::hidden('order_id', $order->id);
                            echo Form::hidden('associado', $order->associated);
                            echo Form::checkbox('associado', 1, $order->associated);
                        }
                    @endphp
                    {!! Form::token() !!}
                </div>
                <div class="modal-footer">
                    {!! Form::submit('Concluir!') !!}
                    {!! Form::close() !!}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <meta name="_token" content="{!! csrf_token() !!}" />
    <script src="{{asset('js/ajax-crud.js')}}"></script>

@endsection
@section('scripts')
    <script>
        function myFunction1($id) {
            document.getElementById($id).stepUp(1);
        }
        function myFunction2($id) {
            document.getElementById($id).stepDown(1);
        }
        $('#tabsCategorias > ul> li:last').click(function (e) {
            e.preventDefault();
            window.location = $('#tabsCategorias').attr('data-url');
        });

    </script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/i18n/defaults-*.min.js"></script>

@endsection