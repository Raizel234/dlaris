<?php

namespace App\Http\Middleware;

use App\Models\LogActivity as ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $params = $request->route()->parameters();
            $modelId = null;
            if (!empty($params)) {
                $modelId = end($params);
                $modelId = is_numeric($modelId) ? (int) $modelId : $modelId;
            }

            ActivityLog::create([
                'user_id'     => $request->user()->id,
                'action'      => strtolower($request->method()),
                'model_type'  => $request->route()->getName(),
                'model_id'    => $modelId,
                'description' => $this->buildDescription($request),
                'after'       => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        return $response;
    }

    private function buildDescription(Request $request): string
    {
        $routeName = $request->route()->getName();
        $action = match ($request->method()) {
            'POST'   => 'membuat',
            'PUT', 'PATCH' => 'mengubah',
            'DELETE' => 'menghapus',
            default  => 'memproses',
        };

        return "{$action} {$routeName}";
    }
}
