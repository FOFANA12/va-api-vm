<?php

namespace App\Repositories\Auth;

use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Resources\Auth\UserProfileResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileRepository
{
    /**
     * Get the user's profile.
     */
    public function getProfile()
    {
        $user = Auth::user()->load(['employee.structure', 'role.permissions']);
        return new UserProfileResource($user);
    }

    /**
     * Update the user's profile.
     */
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $user->fill($request->only([
                'name',
                'phone',
                'email',
                'lang',
            ]));


            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            if ($user->relationLoaded('employee') && $user->employee) {
                $user->employee->update([
                    'job_title' => $request->input('job_title'),
                    'floor' => $request->input('floor'),
                    'office' => $request->input('office'),
                ]);
            }

            DB::commit();

            $user->loadMissing(['employee.structure', 'role.permissions']);
            return new UserProfileResource($user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
