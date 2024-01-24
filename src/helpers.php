<?php

if(! function_exists('routePermission')){
    function routePermission($route, $type = 'permission')
    {
        $permissionMap = [
            // perm => route name suffix
            'list'   => ['index', 'show'],
            'create' => ['create', 'store'],
            'edit'   => ['edit', 'update'],
            'delete' => ['destroy'],
        ];
        foreach ($permissionMap as $perm => $routeSuffixes) {
            if (Str::endsWith($route, $routeSuffixes)) {
                switch ($type) {
                    case 'permission':
                        $segments = explode('.', $route);
                        array_pop($segments);
                        return implode('.', $segments) . '.' . $perm;
                        break;
                        
                    case 'suffix':
                        return $perm;

                    default:
                        return $perm;
                        break;
                }
            }
        }
        return $route;
    }
}