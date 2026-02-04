@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'factory'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
@parent
<style>

    .form-label {
        font-weight: 500;
    }

    .btn-warna {
            background-color: #0000e4;
    }
    .btn-warna:hover {
        background-color: #000080;
    }
</style>
@endsection

@section('body')
<main>
    <div class="container pt-4">
        {{-- @include('components.web.change-plant-selector') --}}
        @yield('mobile-title')
        @yield('tab-nav-bar')
        <div class="row mt-4">
            <h5 class="col-6 secondary-text">ADD NEW FACTORY</h5>
            <div class="col-6 text-end">
                {{-- form delete button --}}
                <form action="{{ route('settings.factory.destroy',[ $plant->uid, $factory->id ]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">DELETE RECORD</button>
                </form>
            </div>
        </div>
        <hr>
        <form method="post" action="{{ route('settings.factory.update',[ $plant->uid, $factory->id ]) }}">
            @method('PUT')
            @csrf
            <div class="col-12 mt-3">
                <!-- Data Form -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12 mt-3">
                                <label for="name" class="form-label">FACTORY NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ $factory->name }}" placeholder="Factory Name" required>

                            </div>
                            <div class="col-12 col-md-12 mt-3">
                                <label for="uid" class="form-label">FACTORY UID <span class="text-danger">*</span></label>
                                <input type="text" name="uid" id="uid" class="form-control" placeholder="Factory UID" value="{{ $factory->uid }}" required>
                                @error('uid')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-end m-3 mt-0">
                        <a href="{{ route('settings.factory.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                        <button type="submit" class="btn btn-action btn-warna">SUBMIT</button>
                    </div>
                </div>
            </div>
        </form>

    </div>


</main>
@endsection

@section('scripts')
@parent
<script>
    //TODO: Guard variable
    $(() => {
        page.initialize();

    });


    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }

    //function. text from name to lowercase and change spaces to '-' , ignore special character
    var element = document.getElementById('name');
    element.addEventListener('input', function() {
        var text = this.value;
        text = text.toLowerCase();
        text = text.replace(/\s/g, '-');
        text = text.replace(/[^a-z0-9-]/g, '');
        document.getElementById('uid').value = text;
    });
</script>
@endsection