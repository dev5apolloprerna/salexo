@extends('layouts.client')
@section('title', 'Lead Calendar')

@section('content')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Lead Calendar</h5>
                        <hr>
                    </div>
                    <div class="card-body">
                        <form id="filter-form">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="employee_id_filter" class="form-label">Employee</label>
                                    <select name="employee_id" id="employee_id_filter" class="form-control">
                                        <option value="">All Employees</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->emp_id }}">{{ $emp->emp_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2 mt-3 mt-md-0">
                                    <button type="button" class="btn btn-primary" id="searchAppointments">Search</button>
                                    <button type="button" class="btn btn-secondary" id="resetFilters">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br><br>
                <!-- Calendar -->
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

    <script>
        $(document).ready(function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [],
                eventContent: function(arg) {
                    return {
                        html: `<div style="white-space: normal; text-align: center;">${arg.event.title.replace(' with', '<br>with')}</div>`
                    };
                }
            });

            calendar.render();

            function fetchAppointments() {
                var employeeId = $('#employee_id_filter').val();

                $.ajax({
                    url: '{{ route('clients.calender.getLeads') }}',
                    method: 'GET',
                    data: {
                        employee_id: employeeId
                    },
                    success: function(data) {
                        if (!Array.isArray(data)) {
                            alert("Invalid data received from server.");
                            return;
                        }

                        calendar.removeAllEvents();
                        calendar.addEventSource(data);
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Failed to fetch leads.");
                    }
                });
            }

            // Load all leads initially
            fetchAppointments();

            $('#searchAppointments').on('click', function() {
                fetchAppointments();
            });

            $('#resetFilters').on('click', function() {
                $('#employee_id_filter').val('');
                fetchAppointments();
            });
        });
    </script>
@endsection
