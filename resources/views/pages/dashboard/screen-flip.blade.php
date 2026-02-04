@extends('layouts.dashboard')
@include('components.commons.websocket')

@section('head')
    @parent
    <style>
        /* Add any custom styles here */
    </style>
@endsection

@section('body')
    <main id="flipping-content">
        <!-- Initial content will be loaded via JavaScript -->
    </main>
@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentScreen = 1;
            let workCenterUid = '{{ $workCenterUid }}'; // replace with actual work_center_uid
            let plantUid = '{{ $plantUid }}'; // Use the passed plantUid from the controller

            function loadScreen(screenNumber) {
                fetch(`/dashboard/${plantUid}/${workCenterUid}/screen/${screenNumber}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('flipping-content').innerHTML = html;
                    })
                    .catch(error => console.error('Error loading screen:', error));
            }

            loadScreen(currentScreen);

            setInterval(() => {
                currentScreen = currentScreen === 1 ? 8 : 1;
                loadScreen(currentScreen);
            }, 3000);
        });
    </script>
@endsection
