<?php

declare(strict_types=1);

namespace components;

use common\AppException;

class UrlManager
{
    // public const ROUTE_INDEX = '/index';
    public const ROUTE_SUBSCRIBER_INDEX = '/subscriber/index';
    public const ROUTE_SUBSCRIBER_VIEW = '/subscriber/view';
    public const ROUTE_SUBSCRIBER_CREATE = '/subscriber/create';
    public const ROUTE_SUBSCRIBER_UPDATE = '/subscriber/update';
    public const ROUTE_SUBSCRIBER_DELETE = '/subscriber/delete';

    /**
     * @return list<array{url:string,route:string,valid_GETs:string[]}>
     */
    protected function urlMap(): array
    {
        return [
            // ['url' => '/', 'route' => static::ROUTE_INDEX, 'valid_GETs' => []],
            // ['url' => '/subscriber', 'route' => static::ROUTE_SUBSCRIBER_INDEX, 'valid_GETs' => []],
            ['url' => '/', 'route' => static::ROUTE_SUBSCRIBER_INDEX, 'valid_GETs' => []],
            ['url' => '/subscriber/create', 'route' => static::ROUTE_SUBSCRIBER_CREATE, 'valid_GETs' => []],
            ['url' => '/subscriber/view', 'route' => static::ROUTE_SUBSCRIBER_VIEW, 'valid_GETs' => ['id']],
            ['url' => '/subscriber/update', 'route' => static::ROUTE_SUBSCRIBER_UPDATE, 'valid_GETs' => ['id']],
            ['url' => '/subscriber/delete', 'route' => static::ROUTE_SUBSCRIBER_DELETE, 'valid_GETs' => ['id']],
        ];
    }

    /**
     * @throws AppException with a 301 or 404 status code.
     */
    public function resolveRequest(Request $request, bool $strictGETs = false): ResponseHeader
    {
        $originalUrl = $request->getPathInfo();

        $normalisedUrl = strtolower(rtrim($originalUrl, '/'));
        if ('' === $normalisedUrl) {
            $normalisedUrl = '/';
        }
        $responseHeader = new ResponseHeader();

        foreach ($this->urlMap() as $url) {
            if ($url['url'] === $normalisedUrl) {
                if (($normalisedUrl === $originalUrl)
                    && (!$strictGETs || empty($invalidGETs = array_diff_key(
                        $request->getParams()['GET'],
                        array_flip($url['valid_GETs'])
                    )))
                ) {
                    $responseHeader->statusCode = 200;
                    $responseHeader->route = $url['route'];
                } else {
                    // $responseHeader->statusCode = 301;
                    // $responseHeader->location = $normalisedUrl;
                    if ($strictGETs && isset($invalidGETs)) {
                        $request->normaliseGETs($invalidGETs);
                    }
                    throw new AppException($normalisedUrl, 301);
                }
                return $responseHeader;
            }
        }

        // $responseHeader->statusCode = 404;
        throw new AppException('Page not found.', 404);

        return $responseHeader;
    }
}
