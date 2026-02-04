@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'pps'])
@include('templates.search-field-text')
@include('templates.search-field-select')

@section('head')
    @parent
    <style>
        .table {
            background-color: #FFFFFF;
        }
        .table thead{
            background-color: #E1E1E1;
        }
        .table tbody {
            font-weight: 500;
            color: #575353;
        }

        /* inactive: 90px */
        /* active : 80px */

        .status-container {
            width: 100px;
            border: 1px solid #575353;
            font-weight: 500;
            text-align: center;
            border-radius: 5px;
            color: #000000;
            pointer-events: none;
        }

        .status-container .status-active {
            width: 90px;
            background-color: #52AF61;
            border-radius: 5px;
        }

        .status-container .status-inactive {
            width: 90px;
            background-color: #FE5F6D;
            border-radius: 5px;
        }

        .list-action a {
        font-size: 1.2em;
        color: brown;
    }

    .table-container {
            overflow-x: hidden;
    }

    .form-label {
        font-weight: 500;
    }

    .form-labels {
        font-weight: 450;
        color: #7a7a7a;
        font-size: 15px;
    }

    .dull2 {
        background-color: #dddddd;
    }

    .dull {
        background-color: #dddddd;
        border: 1px solid rgb(158, 158, 158);
        border-radius: 2px;
        color: #979797;
        display: none;
    }

    .gerak {
        margin-top: -7px !important;
    }

    .tsize {
        font-size: 15px;
    }

    .tcolo {
        color: #575353 !important;
        font-weight: 500;
    }

    .btn-warna {
        background-color: #0000e4;
    }
    .btn-warna:hover {
        background-color: #000080;
    }

    .editwarna {
        background-color: #ffffff;
        border: 1px solid #ffffff;
        color: #800000;
        font-size: 1.3rem;
    }

    .scwarna {
        background-color: #800000;
        border: 3px solid #0000e4;
    }

    .grksc {
        float: right;
        background-color: #ffffff;
        border: 1.5px solid #800000;
        color: #800000;
    }

    .grksc:hover {
        float: right;
        background-color: #F1E3E3;
        border: 1.5px solid #800000;
        color: #800000;
    }

    </style>
@endsection

@section('body')
<main>
    <div class="container">
        @yield('mobile-title')
        @include('components.web.change-plant-selector')
        @yield('tab-nav-bar')
        <h5 class="mt-3" style="color: #000080">PPS - Production Planning Sheet</h5>
        <hr>

        <div class="container">

            <form id="input-form" method="post" action="{{ route('settings.pps.csv',[ $plant->uid ]) }}" autocomplete="on">
                @csrf
                <div class="row px-3">
                    <div class="p-2 col-12 col-md-6">
                        <div class="card p-3 h-100">
                            <label for="sequence" class="form-label tsize dull">SEQUENCE <span class="text-danger">*</span></label>
                            <input class="form-control tsize dull" type="number" id="sequence" name="sequence" required readonly value="1">
                        
                            <label for="rel" class="form-label tsize dull">REL</label>
                            <input type="text" id="rel" class="dull" name="rel" value="REL" readonly required>

                            <label for="test" class="form-label tsize dull">3303</label>
                            <input type="number" id="test" class="dull" name="test" value="3303" readonly required>

                            <label for="line" class="form-label tsize dull">LINE <span class="text-danger"></span></label>
                            <input class="form-control tsize dull" type="number" id="line" name="line" value="1" readonly required>

                            {{--
                            <label for="workcenter" class="form-label tsize">WORK CENTER <span class="text-danger">*</span></label>
                                <select class="form-select tsize" type="text" id="workcenter" name="workcenter" required>
                                    <option value="L8">L8</option>
                                    <!--<option value="R2A">R2A</option>
                                    <option value="R2G">R2G</option>
                                    <option value="R3U">R3U</option>-->
                                </select><br>
                            --}}

                            <label for="workcenter" class="form-label tsize">WORK CENTER <span class="text-danger">*</span></label>
                                <select class="form-select tsize" id="workcenter" name="workcenter" required>
                                    <option value="" selected disabled> </option>
                                    @foreach ($workcenters as $workcenter)
                                        <option value="{{ $workcenter->name }}">{{ $workcenter->name }}</option>
                                    @endforeach
                                </select><br>                                
                            
                            <div class="row">

                            <div class="col-12 col-md-6">
                            <label for="po" class="form-label tsize">PRODUCTION ORDER <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="text" id="po" name="po" required><br>
                            </div>

                            <div class="col-12 col-md-6">
                            <label for="shift" class="form-label tsize">SHIFT <span class="text-danger">*</span></label>
                            <select class="form-select tsize" type="text" id="shift" name="shift" required>
                                <option value="" selected disabled> </option>
                                <option value="D/S">D/S</option>
                                <option value="N/S">N/S</option>
                            </select><br>
                            </div>
                            </div>

                            <div class="row">

                                <div class="col-12 col-md-6">

                                    {{--
                                    <label for="pno" class="form-label tsize">PART NUMBER <span class="text-danger">*</span></label>
                                    <select class="form-select tsize" type="text" id="pno" name="pno" onchange="updatePartName()" required>
                                        <option value=""></option>
                                        <option value="PW935285">PW935285</option>
                                        <option value="PW935286">PW935286</option>
                                        <option value="PW935289">PW935289</option>
                                        <option value="PW935290">PW935290</option>
                                    </select><br>
                                    --}}

                                    <label for="pno" class="form-label tsize">PART NUMBER <span class="text-danger">*</span></label>
                                    <select class="form-select tsize" id="pno" name="pno" onchange="updatePartName()" required>
                                        <option value="" selected disabled></option>
                                        @foreach($parts as $part)
                                            <option value="{{ $part->part_no }}" data-name="{{ $part->name }}">{{ $part->part_no }}</option>
                                        @endforeach
                                    </select><br>

                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="pna" class="form-label tsize">PART NAME <span class="text-danger">*</span></label>
                                    <input class="form-control tsize" type="text" id="pna" name="pna" readonly required>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="p-2 col-12 col-md-6">
                        <div class="card p-3 h-100">
                            <label for="output" class="form-label tsize">PLAN OUTPUT &#40;PCS&#41; <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="number" id="output" name="output" required><br>

                            <div class="row">

                            <div class="col-12 col-md-6">
                            <label for="sd" class="form-label tsize">PLAN START DATE <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="date" id="sd" name="sd" required><br>
                            </div>

                            <div class="col-12 col-md-6">
                            <label for="st" class="form-label tsize">PLAN START TIME <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="time" id="st" name="st" required><br>
                            </div>
                            </div>

                            <div class="row">

                            <div class="col-12 col-md-6">
                            <label for="ed" class="form-label tsize">PLAN END DATE <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="date" id="ed" name="ed" required><br>
                            </div>

                            <div class="col-12 col-md-6">
                            <label for="et" class="form-label tsize">PLAN END TIME <span class="text-danger">*</span></label>
                            <input class="form-control tsize" type="time" id="et" name="et" required><br>
                            </div>
                            </div>

                            <!--<label for="shift" class="form-label tsize dull">SHIFT <span class="text-danger">*</span></label>
                            <input class="form-control tsize dull" type="text" id="shift" name="shift" value="D/S" readonly required>-->

                            <label for="unit" class="form-label tsize dull">UNIT <span class="text-danger">*</span></label>
                            <input type="text" id="unit" class="dull" name="unit" value="PCS" readonly required>
                        
                            <!-- Add other form fields as needed -->
        
                        </div>

                    </div>
                    <div class="text-end">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="button" onclick="addData()" class="btn btn-action btn-warna">Add to Table</button>
                    </div>
                </div><br>
                
            </form>

            <div class="search-box mt-3">
                <div class="search-header p-1 px-2 collapsed" style="background-color: #000080" data-bs-toggle="collapse" href="#search-main-table" role="button" aria-expanded="false" aria-controls="search-main-table">
                    SEARCH &nbsp;<i class="fas fa-chevron-up chevron"></i>
                </div>
                <div id="search-main-table" class="collapse">
                    <div class="search-container">
                        <div class="row px-3">
                            <div class="col-12 col-md-2">
                                <label for="search-woc" class="form-labels">Work Center</label>
                                <input type="text" class="form-control tsize tcolo " id="search-woc">
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="search-po" class="form-labels">Production Order</label>
                                <input type="text" class="form-control tsize tcolo" id="search-po">
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="search-pno" class="form-labels">Part Number</label>
                                <input type="text" class="form-control tsize tcolo" id="search-pno">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="search-pna" class="form-labels">Part Name</label>
                                <input type="text" class="form-control tsize tcolo" id="search-pna">
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="search-plo" class="form-labels">Plan Output</label>
                                <input type="text" class="form-control tsize tcolo" id="search-plo">
                            </div>
                        </div>
                        <div class="row px-3">
                            <div class="col-12 col-md-2">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add this table above your form to display submitted values -->
            <table class="table table-striped w-100 tsize" id="data-table">
                <thead>
                    <tr>
                        <th>Seq</th>
                        <th>REL</th>
                        <th>Def</th>
                        <th>Line</th>
                        <th>Work Center</th>
                        <th>Production Order</th>
                        <th>Part Number</th>
                        <th>Part Name</th>
                        <th>Shift</th>
                        <th>Plan Start Date</th>
                        <th>Plan Start Time</th>
                        <th>Plan End Date</th>
                        <th>Plan End Time</th>
                        <th>Plan Output</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Submitted values will be added dynamically here -->
                </tbody>
            </table>
            <div class="text-end">
                <button type="button" class="btn btn-secondary gerak" onclick="clearTable()">Cancel PPS</button>
                <button id="submit-pps-button" type="button" onclick="submitData()" class="btn btn-action gerak btn-warna">Submit PPS</button>
            </div>
        </div>

    </div>
</main>
@endsection

@section('modals')
@parent
<div>

</div>
@endsection

@section('scripts')
@parent
<script>

    //function updatePartName() {
        //var selectedPartNumber = document.getElementById("pno").value;
        //var partNameInput = document.getElementById("pna");

        // You can customize this part to match your actual data structure
        // For simplicity, I'm using a switch statement here
        //switch (selectedPartNumber) {
            //case "PW935285":
                //partNameInput.value = "Hinge FR Dr Upr LH";
                //break;
            //case "PW935286":
                //partNameInput.value = "Hinge FR Dr Upr RH";
                //break;
            //case "PW935289":
                //partNameInput.value = "Hinge FR Dr Lwr LH";
                //break;
            //case "PW935290":
                //partNameInput.value = "Hinge FR Dr Lwr RH";
                //break;
            // Add more cases as needed
            //default:
                //partNameInput.value = ""; // Clear the input if no match
                //break;
        //}
    //}

    document.addEventListener("DOMContentLoaded", function() {
        // Load data from localStorage
        const storedData = localStorage.getItem("tableData");
        if (storedData) {
            data = JSON.parse(storedData);
            updateTable();
        }
    });

    function updatePartName() {
        const pno = document.getElementById('pno');
        const pna = document.getElementById('pna');
        const selectedOption = pno.options[pno.selectedIndex];
        pna.value = selectedOption ? selectedOption.getAttribute('data-name') : '';
    }

    let data = [];
    let editMode = false; // Add this variable to track edit mode state

    function addData() {
        const table = document.getElementById("data-table");
        const sequence = table.rows.length; // Auto-increment based on the number of rows
        const rel = document.getElementById("rel").value;
        const test = document.getElementById("test").value;
        const line = document.getElementById("line").value;
        const workcenter = document.getElementById("workcenter").value;
        const po = document.getElementById("po").value;
        const pno = document.getElementById("pno").value;
        const pna = document.getElementById("pna").value;
        const shift = document.getElementById("shift").value;
        const sd = document.getElementById("sd").value;
        const st = document.getElementById("st").value;
        const ed = document.getElementById("ed").value;
        const et = document.getElementById("et").value;
        const output = document.getElementById("output").value;
        const unit = document.getElementById("unit").value;

        if (sequence && rel && test && line && workcenter && po && pno && pna && sd && st && ed && et && shift && output && unit) {
            data.push([sequence, rel, test, line, workcenter, po, pno, pna, sd, st, ed, et, shift, output, unit]);
            
            // Save data to localStorage
            localStorage.setItem("tableData", JSON.stringify(data));

            // Clear only specific input fields
            document.getElementById("sequence").value = sequence;
            document.getElementById("rel").value = "REL";
            document.getElementById("test").value = "3303";
            document.getElementById("line").value = "1";
            document.getElementById("workcenter").value = "";
            document.getElementById("po").value = "";
            document.getElementById("pno").value = "";
            document.getElementById("pna").value = "";
            document.getElementById("shift").value = "";
            document.getElementById("sd").value = "";
            document.getElementById("st").value = "";
            document.getElementById("ed").value = "";
            document.getElementById("et").value = "";
            document.getElementById("output").value = "";
            document.getElementById("unit").value = "PCS";
        } else {
            alert("Please fill in all fields.");
        }

        updateTable();
    }

    function updateTable() {
        const table = document.getElementById("data-table");
        // Clear the existing rows
        table.innerHTML = "<tr><th>Seq</th><th>Rel</th><th>Def</th><th>Line</th><th>Work Center</th><th>Production Order</th><th>Part Number</th><th>Part Name</th><th>Start Date</th><th>Start Time</th><th>End Date</th><th>End Time</th><th>Shift</th><th>Output</th><th>Unit</th><th>Action</th></tr>";

        // Add rows with editable cells
        data.forEach((row, rowIndex) => {
            const newRow = table.insertRow(-1);
            for (let i = 0; i < row.length; i++) {
                const cell = newRow.insertCell(i);
                const input = document.createElement("input");
                input.type = "text";
                input.value = row[i];

                // Set input to be disabled for specific columns (e.g., columns 1, 2, and 3) -disable edit some row-
                if ([0, 1, 2, 3, 4, 14].includes(i)) {
                    input.disabled = true;
                } else {
                    input.disabled = !editMode;
                }

                input.style.width = "calc(100% - 1px)";
                cell.appendChild(input);
            }

            const actionCell = newRow.insertCell(row.length);

            // Add "Edit" button
            const editButton = createButton("", function () {
                toggleEditMode(newRow);
            });
            editButton.classList.add("fa-solid", "fa-pen-to-square", "editwarna"); // Add Bootstrap button styles
            actionCell.appendChild(editButton);
        });

        // Add "Save Changes" button when in edit mode
        if (editMode) {
            const saveChangesButton = createButton("Save Changes", function () {
                saveChanges();
            }, false);
            saveChangesButton.classList.add("btn", "btn-primary", "grksc"); // Add Bootstrap button styles

            const saveChangesRow = table.insertRow(-1);
            const saveChangesCell = saveChangesRow.insertCell(0);
            saveChangesCell.colSpan = "16";
            saveChangesCell.appendChild(saveChangesButton);
        }
    }

    function toggleEditMode(row) {
        editMode = !editMode;
        updateTable();
    }

    function createButton(label, onClick, isDisabled = false) {
        const button = document.createElement("button");
        button.innerHTML = label;
        button.onclick = onClick;
        button.disabled = isDisabled;
        button.style.margin = "5px";
        return button;
    }

    function saveChanges() {
        const table = document.getElementById("data-table");
        const rows = table.rows;

        // Iterate through rows to save changes
        for (let i = 1; i < rows.length - 1; i++) { // Start from 1 to skip header and save changes row
            const cells = rows[i].cells;
            const updatedRow = [];

            for (let j = 0; j < cells.length - 1; j++) {
                const inputValue = cells[j].querySelector("input").value;
                updatedRow.push(inputValue);
            }

            data[i - 1] = updatedRow; // Update the corresponding data row
        }

        // Save data to localStorage
        localStorage.setItem("tableData", JSON.stringify(data));

        // Reset edit mode and update the table
        editMode = false;
        updateTable();
    }

    function clearTable() {
        data = [];
        localStorage.removeItem("tableData"); // Clear the data from localStorage
        updateTable();
    }

    function submitData() {
        if (data.length === 0) {
            alert("No data to submit.");
            return;
        }

        if (editMode) {
            // Display alert if edit mode is still active
            alert("Please save the changes before submitting.");
            return;
        }

        // Attach data to the form before submission
        const form = document.getElementById("input-form");
        const dataInput = document.createElement("input");
        dataInput.type = "hidden";
        dataInput.name = "data";
        dataInput.value = JSON.stringify(data);
        form.appendChild(dataInput);

        // Display submission success alert
        alert("Form submitted successfully!");

        // Submit the form
        form.submit();

        // Clear the data array, update the table, and remove data from localStorage
        data = [];
        editMode = false; // Reset edit mode state
        localStorage.removeItem("tableData");
        updateTable();

        // Prevent the default form submission
        return false;
    }
</script>

@endsection

@section('modals')
@parent
<div>

</div>
@endsection