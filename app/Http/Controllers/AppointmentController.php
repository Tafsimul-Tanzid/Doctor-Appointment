<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::orderBy('appointment_date', 'desc')->paginate(10);
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('appointments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'appointment_date' => 'required|date',
            'department_id' => 'required',
            'doctors' => 'required|array',
            'doctors.*' => 'exists:doctors,id',
            'patient_name' => 'required',
            'patient_phone' => 'required',
            'paid_amount' => 'required|numeric',
        ]);

        // Generate appointment number based on current date time and appointment ID
        $appointmentNo = date('YmdHis') . '-' . Appointment::count() + 1;

        // Create appointment
        $appointment = new Appointment();
        $appointment->appointment_no = $appointmentNo;
        $appointment->appointment_date = $validatedData['appointment_date'];
        $appointment->patient_name = $validatedData['patient_name'];
        $appointment->patient_phone = $validatedData['patient_phone'];
        $appointment->total_fee = 0;
        $appointment->paid_amount = $validatedData['paid_amount'];
        $appointment->save();

        // Attach doctors to the appointment
        $doctors = $request->input('doctors');
        $appointment->doctors()->attach($doctors);

        // Calculate total fee based on selected doctors
        $totalFee = Doctor::whereIn('id', $doctors)->sum('fee');
        $appointment->total_fee = $totalFee;
        $appointment->save();

        return redirect('/appointments')->with('success', 'Appointment created successfully.');
    }

    public function fetchDoctors(Request $request)
    {
        $departmentId = $request->department_id;
        $doctors = Doctor::where('department_id', $departmentId)->get();

        $html = '<option selected disabled>Select doctor</option>';
        foreach ($doctors as $doctor) {
            $html .= '<option value="' . $doctor->id . '">' . $doctor->name . '</option>';
        }
        return response()->json($html);
    }
}
