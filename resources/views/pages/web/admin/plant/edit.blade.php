@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'plant'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
@parent
<style>
    /* TODO: Responsive grid layout */
    .form-grid {
        display: grid;
        grid-template-columns: auto;
        grid-template-rows: auto auto;
        row-gap: 1em;
        column-gap: 1em;
    }

    .data-form-cell {
        grid-column-start: 1;
        grid-column-end: 2;
        grid-row-start: 1;
        grid-row-end: 2;
    }

    .submit-button-cell {
        grid-column-start: 1;
        grid-column-end: 2;
        grid-row-start: 2;
        grid-row-end: 3;
    }

    .profile-pic-container {
        padding: 1em;
    }

    .profile-pic img {
        width: 120px;
        height: 120px;
        border: 2px solid black;
        border-radius: 50%;
    }
</style>
@endsection

@section('body')
<main>
    <div class="container">
        @yield('mobile-title')
        @yield('tab-nav-bar')

        <h5 class="secondary-text mt-4">EDIT PLANT</h5>
        <hr>
        <form method="post" enctype="multipart/form-data" action="{{ route('admin.plant.update',[$plant->id]) }}">
            @method('put')
            @csrf
            <div class="form-grid mb-4">
                <!-- Data Form -->
                <div class="card data-form-cell">
                    <div class="card-body">
                        <div class="my-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="PLANT NAME" value="{{ old('name',$plant->name) }}">
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="uid" class="form-label">UID<span class="text-danger">*</span></label>
                            <input type="text" name="uid" class="form-control @error('uid') is-invalid @enderror" id="uid" placeholder="PLANT UID" value="{{ old('uid',$plant->uid) }}">
                            @error('uid')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="sap_id" class="form-label">SAP ID<span class="text-danger">*</span></label>
                            <input type="text" name="sap_id" class="form-control @error('sap_id') is-invalid @enderror" id="sap_id" placeholder="SAP ID" value="{{ old('sap_id',$plant->sap_id) }}">
                            @error('uid')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="time_zone" class="form-label">Time Zone<span class="text-danger">*</span></label>
                            <?php $selectedTimeZone = old('time_zone', $plant->time_zone);
                            $timeZones = \DateTimeZone::listIdentifiers(); ?>
                            <select id="time_zone" name="time_zone" class="form-select">
                                @foreach($timeZones as $timeZone)
                                <option <?php echo ($timeZone == $selectedTimeZone ? 'selected' : '') ?>>{{ $timeZone }}</option>
                                @endforeach
                            </select>
                            @error('time_zone')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="region_id" class="form-label">Region<span class="text-danger">*</span></label>
                            <select id="region_id" name="region_id" class="form-select">
                                @foreach($regions as $region)
                                <option value="{{ $region->id }}" <?php echo (old('region_id', $plant->region_id) == $region->id ? 'selected' : '') ?>>{{ $region->name }}</option>
                                @endforeach
                            </select>
                            @error('region_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_id" class="form-label">Company<span class="text-danger">*</span></label>
                            <select id="company_id" name="company_id" class="form-select">
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" <?php echo (old('company_id', $plant->company_id) == $company->id ? 'selected' : '') ?>>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total_employee" class="form-label">Total Employee<span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1" name="total_employee" class="form-control @error('total_employee') is-invalid @enderror" id="total_employee" placeholder="Total Employee" value="{{ old('total_employee',$plant->total_employee) }}">
                            @error('total_employee')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total_production_line" class="form-label">Total Production Line<span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1" name="total_production_line" class="form-control @error('total_production_line') is-invalid @enderror" id="total_production_line" placeholder="Total Production Line" value="{{ old('total_production_line',$plant->total_production_line) }}">
                            @error('total_production_line')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="overview_layout_file" class="form-label">Overview Layout Data<span class="text-danger">*</span></label>
                            <input name="overview_layout_file" class="form-control" type="file" id="overview_layout_file" accept=".svg">
                            @error('overview_layout_file')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="submit-button-cell text-end">
                    <a href="{{ route('admin.plant.index') }}" class="btn btn-secondary">Cancel</a>
                    <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">Delete</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>

    </div>


</main>
@endsection

@section('modals')
@parent
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationLabel">Delete Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Delete plant?<br>
                Name: <strong>{{ $plant->name }}</strong><br>
                UID: <strong>{{ $plant->uid }}</strong><br>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" action="{{ route('admin.plant.destroy',[ $plant->id ]) }}">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(() => {
        page.initialize();
    });

    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }
</script>
@endsection