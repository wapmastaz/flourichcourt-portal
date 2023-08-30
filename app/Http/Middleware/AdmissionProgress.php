<?php

namespace App\Http\Middleware;

use App\SmParent;
use App\SmStudent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class AdmissionProgress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $parent = SmParent::where('user_id', Auth::user()->id)->first();
            // check if parent has child that has ongoing admission
            $child = SmStudent::where('parent_id', $parent->id)
                ->where('status', 0)
                ->where('student_category_id', 1)
                ->first();
            if ($child) {
                // redirect
                Toastr::info('Your child admission is ongoing at the moment.', 'Important Information');
                return redirect('/admission-progress');
            }
        }
        return $next($request);
    }
}
