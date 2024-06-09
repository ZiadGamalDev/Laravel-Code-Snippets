<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\AddressRequest;
use App\Models\Address;
use App\Models\User;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::with('user:id,full_name')->paginate(10);

        return view('addresses.index', ['dataTable' => $addresses]);
    }
    
    public function create()
    {
        $users = User::where('role_id', '!=', 1)->pluck('full_name', 'id');

        return view('addresses.create', compact('users'));
    }
    
    public function store(AddressRequest $request)
    {
        $requestData = $request->validated();
        
        $address = Address::create($requestData);

        return redirect()->route('addresses.index')->withSuccess(trans('lang.created_success'));
    }
    
    public function edit(Address $address)
    {
        $users = User::where('role_id', '!=', 1)->pluck('full_name', 'id');

        return view('addresses.edit', compact('address', 'users'));
    }

    public function update(AddressRequest $request, Address $address)
    {
        $requestData = $request->validated();

        $address->update($requestData);

        return redirect()->route('addresses.index')->withSuccess(trans('lang.updated_success'));
    }

    public function destroy(Address $address)
    {
        $address->delete();
        
        return redirect()->route('addresses.index')->withSuccess(trans('lang.deleted_success'));
    }
}
