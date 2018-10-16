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

    protected $headers = [];

    public function __construct(
        Client $client = null,
        LoggerInterface $logger = null,
        $consulAddress = null
    ) {
        $this->client = $client ?: new Client();
        $this->logger = $logger ?: new NullLogger();
        $this->consulAddress = $consulAddress ?: 'http://127.0.0.1:8500';
    }

    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function addToken(string $token)
    {
        return $this->addHeader('X-Consul-Token', $token);
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
        $params['headers'] = $this->headers;

        try {
            /** @var \Psr\Http\Message\ResponseInterface $consulRequest */
            $consulRequest = $this->client->{$method}($url, $params);
        } catch (\Exception $e) {
            $message = sprintf('Failed to to perform request to consul (%s).', $e->getMessage());
            $this->logger->error($message);
            throw new ServerException($message);
        } finally {
            $this->headers = [];
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
}
