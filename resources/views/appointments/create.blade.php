@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Appointment</h1>
        <form action="{{ route('appointments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <option selected disabled>Select department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="doctor_id" class="form-label">Doctor</label>
                <select class="form-select" id="doctor_id" name="doctor_id" required>
                    <option selected disabled>Select doctor</option>
                    <!-- Add options for doctors here -->
                </select>
            </div>

            <div class="mb-3">
                <label for="fee" class="form-label">Fee</label>
                <input type="text" class="form-control" id="fee" name="fee" readonly>
            </div>

            <div class="mb-3">
                <div id="availability-message"></div>
            </div>

            <button type="button" class="btn btn-primary" id="add-doctor" disabled>Add Doctor</button>

            <h2>Selected Doctors</h2>
            <table class="table" id="selected-doctors-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Selected doctors will be added dynamically --}}
                </tbody>
            </table>

            <h2>Patient Details</h2>
            <div class="mb-3">
                <label for="patient_name" class="form-label">Patient Name</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" required>
            </div>

            <div class="mb-3">
                <label for="patient_phone" class="form-label">Patient Phone</label>
                <input type="text" class="form-control" id="patient_phone" name="patient_phone" required>
            </div>

            <div class="mb-3">
                <label for="paid_amount" class="form-label">Paid Amount</label>
                <input type="text" class="form-control" id="paid_amount" name="paid_amount" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Fetch doctors based on selected department
            $('#department_id').change(function() {
                var departmentId = $(this).val();
                if (departmentId) {
                    $('#doctor_id').prop('disabled', false).html('<option>Loading...</option>');
                    $.get("{{ route('doctors.fetch') }}", {
                        department_id: departmentId
                    }, function(data) {
                        $('#doctor_id').html(data);
                        $('#availability-message').html('');
                        $('#add-doctor').prop('disabled', true);
                    });
                } else {
                    $('#doctor_id').prop('disabled', true).html('<option>Select department first</option>');
                    $('#availability-message').html('');
                    $('#add-doctor').prop('disabled', true);
                }
            });

            // Fetch doctor's availability and fee
            $('#doctor_id').change(function() {
                var doctorId = $(this).val();
                var appointmentDate = $('#appointment_date').val(); // Get the appointment date value

                if (doctorId &&
                    appointmentDate) { // Check if both doctorId and appointmentDate are available
                    $.get("{{ route('doctors.availability') }}", {
                        doctor_id: doctorId,
                        appointment_date: appointmentDate // Pass the appointment date to the server
                    }, function(data) {
                        if (data.available) {
                            $('#availability-message').html(
                                '<span class="text-success">Doctor is available.</span>');
                            $('#add-doctor').prop('disabled', false);
                        } else {
                            $('#availability-message').html(
                                '<span class="text-danger">Doctor is not available. Please choose another doctor.</span>'
                            );
                            $('#add-doctor').prop('disabled', true);
                        }
                        $('#fee').val(data.fee);
                    });
                } else {
                    $('#availability-message').html('');
                    $('#fee').val('');
                    $('#add-doctor').prop('disabled', true);
                }
            });

            // Add selected doctor to the table
            $('#add-doctor').click(function() {
                var doctorId = $('#doctor_id').val();
                var doctorName = $('#doctor_id option:selected').text();

                if (doctorId && doctorName) {
                    var newRow = `
                    <tr>
                        <td>${doctorName}</td>
                        <td>
                            <input type="hidden" name="doctors[]" value="${doctorId}">
                            <button type="button" class="btn btn-sm btn-danger delete-doctor" title="Remove Doctor">Delete</button>
                        </td>
                    </tr>
                `;

                    $('#selected-doctors-table tbody').append(newRow);
                    $('#doctor_id').val('');
                    $('#availability-message').html('');
                    $('#add-doctor').prop('disabled', true);
                }
            });

            // Delete selected doctor from the table
            $(document).on('click', '.delete-doctor', function() {
                $(this).closest('tr').remove();
            });

            // Validate paid amount
            $('#paid_amount').on('input', function() {
                var paidAmount = parseFloat($(this).val());
                var totalFee = parseFloat($('#fee').val());

                if (paidAmount === totalFee) {
                    $(this).removeClass('is-invalid');
                    $('button[type="submit"]').prop('disabled', false);
                } else {
                    $(this).addClass('is-invalid');
                    $('button[type="submit"]').prop('disabled', true);
                }
            });
        });
    </script>
@endsection
