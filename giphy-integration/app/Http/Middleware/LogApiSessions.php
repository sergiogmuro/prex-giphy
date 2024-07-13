<?php

namespace App\Http\Middleware;

use App\Models\ApiSessionLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogApiSessions
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);

            if (json_decode($response->getContent())) {
                if (Auth::check()) {
                    ApiSessionLog::create([
                        'user_id' => Auth::id(),
                        'service' => $request->path(),
                        'request_body' => json_encode($request->all()),
                        'http_status' => $response->status(),
                        'response' => $response->getContent(),
                        'origin_ip' => $request->ip(),
                    ]);
                }
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Error during API request: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred. Please try again later.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
