<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpFoundation\Response;

class HandlePostTooLarge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (PostTooLargeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The uploaded files are too large.',
                    'errors' => [
                        'files' => ['The total size of uploaded files exceeds the maximum allowed limit.']
                    ]
                ], 413);
            }

            return redirect()->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Upload Too Large',
                    'message' => 'The total size of your files is too large. Please reduce the number of files or compress them.'
                ]);
        }
    }
}
