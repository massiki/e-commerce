<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\LogActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->latest()->get();

        return view('customer.dashboard.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.dashboard.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:1,15',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|numeric|digits_between:1,6',
            'full_address' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();

        Address::create($validated);

        LogActivityService::log("Created address: {$validated['recipient_name']}");

        return redirect()->route('customer.addresses.index')->with('success', 'Address added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);

        return view('customer.dashboard.addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:1,15',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|numeric|digits_between:1,6',
            'full_address' => 'required|string',
        ]);

        $address->update($validated);

        LogActivityService::log("Updated address: {$address->recipient_name}");

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        abort_if($address->user_id !== Auth::id(), 403);

        LogActivityService::log("Deleted address: {$address->recipient_name}");

        $address->delete();

        return redirect()->route('customer.addresses.index')->with('success', 'Address deleted successfully.');
    }
}
