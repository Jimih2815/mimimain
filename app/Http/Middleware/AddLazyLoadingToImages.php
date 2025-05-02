<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddLazyLoadingToImages
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        // Chỉ xử lý HTML (bỏ qua JSON, file, v.v.)
        if (str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $html = $response->getContent();

            /*
             * Regex: lấy mọi <img …> **chưa** có loading=
             *  - Cho phép bạn thêm class "no-lazy" để bỏ qua logo, icon nhỏ
             */
            $pattern     = '/<img(?![^>]*\bloading=)(?![^>]*\bno-lazy)([^>]+?)>/i';
            $replacement = '<img loading="lazy"$1>';

            $html = preg_replace($pattern, $replacement, $html);
            $response->setContent($html);
        }

        return $response;
    }
}
