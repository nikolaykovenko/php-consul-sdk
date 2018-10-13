<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use indigerd\consul\exceptions\ServerException;
use indigerd\consul\exceptions\ClientException;

class BaseService
{
    /** @var Client  */
    protected $client;

    /** @var LoggerInterface  */
    protected $logger;

    protected $consulAddress;

    protected $authToken;

    public function __construct(
        Client $client = null,
        LoggerInterface $logger = null,
        $consulAddress = null,
        $authToken = null
    ) {
        $this->client = $client ?: new Client();
        $this->logger = $logger ?: new NullLogger();
        $this->consulAddress = $consulAddress ?: 'http://127.0.0.1:8500';
        $this->authToken = $authToken;
    }

    protected function get($url, $params = [])
    {
        return $this->request('get', $url, $params);
    }

    protected function put($url, $params = [])
    {
        return $this->request('put', $url, $params);
    }
    protected function delete($url, $params = [])
    {
        return $this->request('delete', $url, $params);
    }

    protected function request($method, $url, $params, $decode = true)
    {
        $url = $this->consulAddress . $url;
        try {
            /** @var \Psr\Http\Message\ResponseInterface $consulRequest */
            $consulRequest = $this->client->{$method}($url, $this->addTokenHeader($params));
        } catch (\Exception $e) {
            $message = sprintf('Failed to to perform request to consul (%s).', $e->getMessage());
            $this->logger->error($message);
            throw new ServerException($message);
        }

        $this->logger->debug(sprintf("Response:\n%s", $consulRequest->getBody()));
        if (400 <= $consulRequest->getStatusCode()) {
            $message = sprintf('Consul responded with error (%s - %s).', $consulRequest->getStatusCode(), $consulRequest->getReasonPhrase());
            $this->logger->error($message);
            $message .= "\n" . $consulRequest->getBody();
            if (500 <= $consulRequest->getStatusCode()) {
                throw new ServerException($message);
            }
            throw new ClientException($message);
        }

        $data = $consulRequest->getBody();
        if ($decode) {
            $data = json_decode($consulRequest->getBody());
        }
        return $data;
    }

    /**
     * Adds consul auth token to request headers
     * @param array $params
     * @return array
     */
    private function addTokenHeader(array $params)
    {
        if ($this->authToken && empty($params['headers']['X-Consul-Token'])) {
            $params['headers']['X-Consul-Token'] = $this->authToken;
        }

        return $params;
    }
}
