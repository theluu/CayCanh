<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bots fill the hidden "website" field; pretend success without touching the database.
 */
class Honeypot
{
    public const FIELD = 'website';

    public function handle(Request $request, Closure $next): Response
    {
        if (filled($request->input(self::FIELD))) {
            return back()->with('success', 'Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm.');
        }

        return $next($request);
    }
}
