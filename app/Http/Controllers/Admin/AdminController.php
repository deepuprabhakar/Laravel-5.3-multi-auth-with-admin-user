<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Admin;
use App\Mail\AdminAdded;
use Mail;
use Datatables;
use DB;
use Form;
use Hashids;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
    }

    /**
     * List all Admins
     * @param  Request $request 
     * @return Datatables
     */
    public function list(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $admins = Admin::select([
                    DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'])->where('email', '!=', env('ADMIN_EMAIL'));

        $datatables = Datatables::of($admins)
            ->addColumn('action', function ($admins) {
                return '<div class="text-center">
                            <a href="'.route('admins.edit', Hashids::connection('admins')->encode($admins->id)).'" class="datatable-action btn btn-primary btn-xs">
                                <i class="fa fa-pencil-square"></i></a>'.
                            
                            Form::open(['url' => route('admins.destroy', Hashids::connection('admins')->encode($admins->id)), 'method' => 'DELETE', 'class' => 'delete-form'])

                            .'<button type="submit" class="datatable-action delete btn btn-danger btn-xs" aria-label="Left Align">
                              <i class="fa fa-trash"></i></span>
                            </button>'.

                            Form::close()

                            .'</div>
                        ';
            })
            ->editColumn('created_at', function ($admins) {
                return $admins->created_at->diffForHumans();
            })
            ->editColumn('updated_at', function ($admins) {
                return $admins->updated_at->diffForHumans();
            })
            ->removeColumn('id');

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }
            
        return $datatables->make(true);
    }

    /**
     * Admin Dashboard
     * @return [type] [description]
     */
    public function dashboard()
    {
        return view('admin.home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
                'name' => 'required|string',
                'email' => 'required|email|unique:admins',
            ]);
        $input = $request->all();
        $password = str_random(6);
        $input['password'] = bcrypt($password);
        $admin = Admin::create($input);
        Mail::to($request->get('email'))->queue(new AdminAdded($admin, $password));
        $res['success'] = "New Admin added successfully!";
        return $res;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('admins.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::find(Hashids::connection('admins')->decode($id))->first();
        if(is_null($admin))
            return view('admin.404');

        return view('admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
                'name' => 'required|string',
            ]);
        $admin = Admin::find(Hashids::connection('admins')->decode($id))->first();
        $data = $admin->update($request->all());
        if(!is_null($data))
        {
            $res['success'] = "Updated!";
            return $res;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::find(Hashids::connection('admins')->decode($id))->first();
        if(is_null($admin))
            return view('admin.404');
        else
        {
            Admin::destroy($admin->id);
            return redirect()->route('admins.index');
        }
    }
}
