<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Student::latest()->paginate(5);
        /**  paginate(5) phân trang dữ liệu với 5 đối tượng trên mỗi trang.
         * latest() được gọi để sắp xếp các đối tượng theo thời gian tạo mới nhất.
         * 
        */

        return view('index', compact('data'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //xử lý thêm sinh viên 
        /**
         * $request->validate() để kiểm tra tính hợp lệ của dữ liệu được gửi lên từ form
         */
        $request->validate([
            'student_email'         =>  'required|email|unique:students',
            'student_image'         =>  'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'

        ]);
        $file_name = time() . '.' . request()->student_image->getClientOriginalExtension();

        request()->student_image->move(public_path('images'), $file_name);

        $student = new Student;

        $student->student_name = $request->student_name;
        $student->student_email = $request->student_email;
        $student->student_gender = $request->student_gender;
        $student->student_image = $file_name;

        $student->save();

        return redirect()->route('students.index')->with('success', 'Student Added successfully.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //hiển thị dữ liệu của một sinh viên
        return view('show', compact('student'));//compact truyền dữ liệu lên view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
        'student_name'      =>  'required',
            'student_email'     =>  'required|email',
            'student_image'     =>  'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
        ]);

        $student_image = $request->hidden_student_image;

        if($request->student_image != '')
        {
            $student_image = time() . '.' . request()->student_image->getClientOriginalExtension();

            request()->student_image->move(public_path('images'), $student_image);
        }

        $student = Student::find($request->hidden_id);

        $student->student_name = $request->student_name;

        $student->student_email = $request->student_email;

        $student->student_gender = $request->student_gender;

        $student->student_image = $student_image;

        $student->save();

        return redirect()->route('students.index')->with('success', 'Student Data has been updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //xóa dữ liệu sinh viên
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student Data deleted successfully');

    }
}
