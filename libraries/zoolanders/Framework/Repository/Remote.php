<?php

namespace Zoolanders\Framework\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Zoolanders\Framework\Cache\Cacheable;

abstract class Remote extends Repository {
    use Cacheable;

    protected $baseUrl = '';

    protected $baseQuery = [];

    protected $isJsonData = true;

    public function __construct (Client $client) {
        $this->client = $client;
    }

    public function all ($force = false) {
        if ($this->force) {
            return $this->fetchFromRemote();
        }

        return $this->cache("list", [$this, "fetchFromRemote"]);
    }

    public function fetchFromRemote ($data = []) {
        try {
            $data = array_merge($this->getQuery(), $data);

            $response = $this->client->get($this->getUrl(), [
                'query' => $data
            ]);

        } catch (RequestException $e) {
            $response = false;
        }

        if ($response && $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            $data = $this->isJsonData ? json_decode($response->getBody(), true) : $response->getBody();
        } else {
            $data = false;
        }

        return $data;
    }

    public function getRemoteUrl ($data = []) {
        $host = $this->getUrl();
        $args = array_merge($this->getQuery(), $data);

        $url = new \JUri($host);
        $url->setQuery($args);

        return $url->toString();
    }

    public function getUrl () {
        return $this->baseUrl;
    }

    public function getQuery () {
        return $this->baseQuery;
    }
}
