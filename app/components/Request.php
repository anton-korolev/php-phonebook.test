<?php

declare(strict_types=1);

namespace components;

use common\AppException;

class Request
{
    private string|null $_pathInfo = null;

    /**
     * Resolves the path info part of the currently requested URL.
     *
     * A path info refers to the part that is after the entry script and before the question mark (query string).
     * All starting and ending slashes will be kept.
     *
     * Taken from the Yii2 framework (see `\yii\base\Request\Request::resolvePathInfo()`).
     *
     * @return string part of the request URL that is after the entry script and before the question mark.
     * Note, the returned path info is already URL-decoded.
     * @throws AppException with a 500 status code if the path info cannot be determined due to unexpected
     * server configuration.
     */
    public function getPathInfo(): string
    {
        if (null !== $this->_pathInfo) {
            return $this->_pathInfo;
        }

        $pathInfo = $this->resolveRequestUri();

        if (($pos = strpos($pathInfo, '?')) !== false) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = urldecode($pathInfo);

        // try to encode in UTF8 if not so
        // https://www.w3.org/International/questions/qa-forms-utf-8.en.html
        if (!preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo)) {
            $pathInfo = $this->utf8Encode($pathInfo);
        }

        $this->_pathInfo = $pathInfo;
        return $this->_pathInfo;
    }

    private string|null $_requestUri = null;

    /**
     * Resolves the request URI portion for the currently requested URL.
     *
     * This refers to the portion that is after the [[hostInfo]] part. It includes the [[queryString]] part if any.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     *
     * Taken from the Yii2 framework (see `\yii\base\Request\Request::resolveRequestUri()`).
     *
     * @return string|bool the request URI portion for the currently requested URL.
     * Note that the URI returned may be URL-encoded depending on the client.
     * @throws AppException with a 500 status if the request URI cannot be determined due to unusual
     * server configuration
     */
    protected function resolveRequestUri(): string
    {
        if (null !== $this->_requestUri) {
            return $this->_requestUri;
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            // https://www.php.net/manual/ru/reserved.variables.server.php#121469
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } else {
            throw new AppException('Unable to determine the request URI.', 500);
        }

        $this->_requestUri = $requestUri;
        return $this->_requestUri;
    }

    /**
     * Encodes an ISO-8859-1 string to UTF-8
     *
     * Taken from the Yii2 framework (see `\yii\base\Request\Request::utf8Encode()`).
     *
     * @param string $s
     * @return string the UTF-8 translation of `s`.
     * @see https://github.com/symfony/polyfill-php72/blob/master/Php72.php#L24
     */
    private function utf8Encode(string $s): string
    {
        $s .= $s;
        $len = \strlen($s);
        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80":
                    $s[$j] = $s[$i];
                    break;
                case $s[$i] < "\xC0":
                    $s[$j] = "\xC2";
                    $s[++$j] = $s[$i];
                    break;
                default:
                    $s[$j] = "\xC3";
                    $s[++$j] = \chr(\ord($s[$i]) - 64);
                    break;
            }
        }
        return substr($s, 0, $j);
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * Returns whether this is a GET request.
     * @return bool whether this is a GET request.
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Returns whether this is a POST request.
     * @return bool whether this is a POST request.
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @var array{GET:array<string,mixed>,POST:array<string,mixed>} the request parameters.
     */
    private array|null $_params = null;

    /**
     * Returns the request parameters (the contents of `$_GET` and `$POST`).
     *
     * @return array{GET:array<string,mixed>,POST:array<string,mixed>} the request parameters.
     */
    public function getParams(): array
    {
        if (null === $this->_params) {
            $this->_params = $this->resolveParams();
        }
        return $this->_params;
    }

    public function GET(): array
    {
        return $this->getParams()['GET'];
    }

    public function POST(): array
    {
        return $this->getParams()['POST'];
    }

    /**
     * Resolves the request parameters (the contents of `$_GET` and `$POST`).
     *
     * @return array{GET:array<string,mixed>,POST:array<string,mixed>} the request parameters.
     * @throws AppException if the request URI cannot be determined due to unusual server configuration
     */
    protected function resolveParams(): array
    {
        return [
            'GET' => $_GET + (('/?' === $this->resolveRequestUri()) ? ['?' => '?'] : []),
            'POST' => $_POST,
        ];
    }

    /**
     * @param array<string,mixed> $invalidGETs invalid _GET parameters.
     * @throws AppException with a 500 status code if the request URI cannot be determined due
     * to unusual server configuration
     */
    public function normaliseGETs(array $invalidGETs): void
    {
        $this->_params['GET'] =  array_diff_key($this->getParams()['GET'], $invalidGETs);
    }
}
