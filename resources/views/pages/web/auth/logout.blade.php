@extends('layouts.guest')
@section('head')
@parent
<style>
    body {
        height: 100vh;
    }
</style>

@endsection
@section('body')
<main class="h-100">
    <div class="container d-flex justify-content-center align-items-center" style="height:100%">
        <div>Logging out...</div>
    </div>
</main>
@endsection
@section('scripts')
@parent
<script>
    $(function() {
        $.ajax({
            url: "{{ route('logout') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            }
        }).always(function(e) {
            window.location.href = "{{ url('/') }}";
        })
    });
</script>
@endsection