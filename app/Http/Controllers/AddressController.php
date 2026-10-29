<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
USE Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller {
    public function store(Request $request) {
        $emailValidator = $this->validateEmail();
        $addressValidator = $this->validateAddress();
        if ($emailValidator->fails() || $addressValidator->fails()) {
            return response()->json([
                'message' => 'Failed',
                'email'   => $emailValidator->messages(),
                'address' => $addressValidator->messages()], 400);
        }

        $user = USER::where('email', $emailValidator->validated()['email'])->firstOrFail();

        if ($user->address) {
            return response()->json(['message' => 'User has address already', 'data' => null], 400);
        }

        $address = new Address($addressValidator->validated());
        if ($user->address()->save($address)) {
            return response()->json(['message' => 'Address save', 'data' => $address], 200);
        }
        return response() -> json(['message' => 'Failed', 'data' => null], 400);
    }

    public function validateAddress() {
        return Validator::make(request()->all(), [
            'country' => 'required|string|min:1|max:5',
            'zipcode' => 'required|string|min:1|max:6',
        ]);
    }

    public function validateEmail() {
        return Validator::make(request()->all(), [
            'email' => 'required|string|email|max:255',
        ]);
    }

    public function show(Address $address) {
        return response()->json(['message' =>'', 'data' => $address], 200);
    }

    public function show_user(Address $address) {
        return response()->json(['message' =>'', 'data' => $address->user], 200);
    }
}
