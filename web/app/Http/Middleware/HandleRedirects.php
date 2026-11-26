<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $target = Redirect::where('from_path', '/'.ltrim($request->path(), '/'))->value('to_path');

            if ($target) {
                return redirect($target, 301);
            }
        }

        return $next($request);
    }
}
