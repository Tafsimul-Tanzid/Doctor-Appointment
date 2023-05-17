<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::orderByDesc('created_at')->get();
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('doctors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'fee' => 'required',
            'department_id' => 'required',
        ]);

        Doctor::create($validatedData);
        return redirect('/doctors')->with('success', 'Doctor created successfully.');
    }

    public function edit(Doctor $doctor)
    {
        $departments = Department::all();
        return view('doctors.edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'fee' => 'required',
            'department_id' => 'required',
        ]);

        $doctor->update($validatedData);
        return redirect('/doctors')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect('/doctors')->with('success', 'Doctor deleted successfully.');
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

    public function checkAvailability(Request $request)
    {
        $doctorId = $request->doctor_id;
        $appointmentDate = $request->appointment_date;

        $doctor = Doctor::find($doctorId);

        $appointmentsCount = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $appointmentDate)
            ->count();

        $response = [
            'available' => $appointmentsCount < 2, // Assuming a doctor can have a maximum of 2 appointments per day
            'fee' => $doctor->fee,
        ];

        return $response;
    }
}
