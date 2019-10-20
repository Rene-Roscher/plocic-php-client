<?php
/**
 * Created by PhpStorm.
 * User: Rene_Roscher
 * Date: 20.10.2019
 * Time: 02:10
 */

namespace Plocic;


use GuzzleHttp\Client;
use Plocic\Manager\PaymentManager;

class Plocic
{

    private $authToken;
	private $uri;
    private $httpClient;

    /**
     * Plocic constructor.
     * @param $authToken
     * @param $uri
     */
    public function __construct($authToken, $uri = 'https://api.plocic.de/v1/')
    {
        $this->authToken = $authToken;
        $this->httpClient = new Client([
            'allow_redirects' => false,
            'timeout' => 120
        ]);
    }

    /**
     * @param array $params
     * @param $action
     * @param $url
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function get(array $params, $action, $url)
    {
        return $this->client($params, 'GET', $action, $url);
    }

    public function post(array $params, $action, $url)
    {
        return $this->client($params, 'POST', $action, $url);
    }

    public function delete(array $params, $action, $url)
    {
        return $this->client($params, 'DELETE', $action, $url);
    }

    public function put(array $params, $action, $url)
    {
        return $this->client($params, 'PUT', $action, $url);
    }

    /**
     * @param array $params
     * @param $method
     * @param $action
     * @param $url
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    private function client(array $params, $method, $action, $url)
    {
        $params['config'] = [];
        $params['config']['timezone'] = 'UTC';
        $params = $this->formatValues($params);

        switch ($method) {
            case 'GET':
                return $this->request($this->httpClient->get($url.$action, [
                    'verify' => false,
                    'query'  => $params,
                ]));
                break;
            case 'POST':
                return $this->request($this->httpClient->post($url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
                break;
            case 'PUT':
                return $this->request($this->httpClient->put($url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
            case 'DELETE':
                return $this->request($this->httpClient->delete($url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
            default:
                return false;
        }
    }

    /**
     * @return DDosManager
     */
    public function getPaymentManager() : PaymentManager
    {
        return new PaymentManager($this);
    }

    /**
     * @param $response
     * @return mixed
     */
    public function request($response)
    {
        $response = $response->getBody()->__toString();
        $result = json_decode($response);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $result;
        } else {
            return $response;
        }
    }

    /**
     * @param array $array
     * @return array
     */
    private function formatValues(array $array)
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $array[$key] = self::formatValues($item);
            } else {
                if ($item instanceof \DateTime)
                    $array[$key] = $item->format("Y-m-d H:i:s");
            }
        }

        return $array;
    }

    /**
     * @return mixed
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @return mixed
     */
    public function getBaseUri()
    {
        return $this->uri;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

}