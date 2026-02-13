<?php

namespace IndieSystems\PermissionsAdminlteUi\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImpersonationBanner
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->session()->has('impersonate_original_id')) {
            return $response;
        }

        // Only inject into HTML responses
        $contentType = $response->headers->get('Content-Type', '');
        if ($contentType !== '' && strpos($contentType, 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();
        if (strpos($content, '</body>') === false) {
            return $response;
        }

        $user = $request->user();
        $name = $user ? e($user->name) : 'Unknown';
        $route = route('users.impersonate.stop');
        $token = csrf_token();

        $banner = <<<HTML
<div id="impersonation-banner" style="position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#dc3545;color:#fff;padding:8px 20px;text-align:center;font-family:sans-serif;">
    <i class="fas fa-user-secret" style="margin-right:8px;"></i>
    You are impersonating <strong>{$name}</strong>.
    <form action="{$route}" method="post" style="display:inline;margin-left:12px;">
        <input type="hidden" name="_token" value="{$token}">
        <button type="submit" style="background:#fff;color:#dc3545;border:none;border-radius:4px;padding:4px 12px;cursor:pointer;font-size:13px;">
            <i class="fas fa-sign-out-alt" style="margin-right:4px;"></i>Stop Impersonating
        </button>
    </form>
</div>
HTML;

        $content = str_replace('</body>', $banner . '</body>', $content);
        $response->setContent($content);

        return $response;
    }
}
