<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

class Uri implements UriInterface
{
    protected $scheme;

    protected $host;

    protected $user;

    protected $pass;

    protected $port;

    protected $path;

    protected $query;

    protected $fragment;

    private $uriString;

    public function __construct($uri)
    {
        $this->parseUrl($uri);
    }

    public function __clone()
    {
        $this->uriString = null;
    }

    protected function parseUrl($uri)
    {
        $parts = $this->extractPartsFromUri($uri);

        $this->scheme = $parts['scheme'];

        $this->host = $parts['host'];

        $this->port = $parts['port'];

        $user = $parts['user'];

        $pass = $parts['pass'];

        $this->user = $user . ($pass ? ':'.$pass : null);

        $this->path = ((strlen($parts['path'])-1) == strrpos($parts['path'], '/') ? substr($parts['path'],0,-1) : $parts['path']);

        $this->query = $parts['query'];

        $this->fragment = $parts['fragment'];
    }

    private function extractPartsFromUri($uri)
    {
        $default = array(
            'scheme'    => 'http',
            'host'      => null,
            'port'      => null,
            'user'      => null,
            'pass'      => null,
            'path'      => '',
            'query'     => '',
            'fragment'  => ''
        );

        $parts = parse_url($uri);

        return array_merge($default, $parts);
    }

    public function withScheme($scheme)
    {
        $new = clone $this;
        $new->scheme = $scheme;
        return $new;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if ($password) {
            $info .= ':' . $password;
        }

        if ($info === $this->user) {
            return clone $this;
        }

        $new = clone $this;
        $new->user = $info;

        return $new;
    }

    public function getUserInfo()
    {
        return $this->user;
    }

    public function withHost($host)
    {
        $new = clone $this;

        $new->host = $host;
        return $new;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function withPort($port)
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function withPath($path)
    {
        $new = clone $this;
        $new->path = (0 !== strpos($path, '/') ? '/' : null)
            . (strlen($path)-1 == strrpos($path, '/')
            ? substr($path, 0, -1)
            : $path);

        return $new;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function withQuery($query)
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function withFragment($fragment)
    {
        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function __toString()
    {
        if (null !== $this->uriString) {
            return $this->uriString;
        }
        $this->uriString = $this->scheme . "://" .
            $this->host .
            ($this->port ? ":".$this->port : null) .
            ($this->path ?: null) .
            ($this->query ? "?".$this->query : null) .
            ($this->fragment ? "#".$this->fragment : null);

        return $this->uriString;
    }
}
