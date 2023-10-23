<?php

namespace OCA\OPNsense\Service;

use Datetime;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCA\OPNsense\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\Http\Client\IClient;
use OCP\IConfig;
use OCP\IL10N;
use OCP\PreConditionNotMetException;
use Psr\Log\LoggerInterface;
use OCP\Http\Client\IClientService;
use Throwable;

class OPNsenseAPIService {

    private IL10N $l10n;
    private LoggerInterface $logger;
	private IConfig $config;
	private IClient $client;
	private string $appVersion;
    private string $apiKey = 'vYHHpQQsII92D60QBehJWzoBkO+kZW5oiyqsTLXOhu+z2BNy8MZ3Ax4nseJbCDF0pW5tQ52bbR+AHPel';
    private string $apiSecret = 'wXHy7GQsLwE/Y2fm1qD1CcQ8e3L7leEsxmEDG5fbOez+pu3s9TKwOxWG1Xz8UeAETMfzaZxyd3WnDydD';

    public function __construct(string $appName,
                                LoggerInterface $logger,
                                IL10N $l10n,
                                IConfig $config,
                                IAppManager $appManager,
                                IClientService $clientService) {

            $this->client = $clientService->newClient();
		    $this->appVersion = $appManager->getAppVersion(Application::APP_ID);
            $this->logger = $logger;
		    $this->l10n = $l10n;
		    $this->config = $config;
    }
    /**
     * @param string $userId
     * @param string $endPoint
     * @param array $params
     * @param string $method
     * @param bool $jsonResponse
     * @throws Exception
     */
    public function restRequest(string $userId,
                                string $endPoint,
                                array $params = [],
                                string $method = 'GET',
                                bool $jsonResponse = true): array {
                                
                                $adminUrl = $this->config->getAppValue(Application::APP_ID, 'admin_instance_url', Application::DEFAULT_OPNSENSE_URL) ?: Application::DEFAULT_OPNSENSE_URL;
		                        $url = $this->config->getUserValue($userId, Application::APP_ID, 'url', $adminUrl) ?: $adminUrl;
                                try {
                                    $url = $url . '/' . $endPoint;
                                    $options = [
                                        'headers' => [
                                            'auth' =>  'Basic '.$apiKey:$apiSecret,
                                        ],
                                    ];
                        
                                    if ($method === 'GET') {
                                        if (count($params) > 0) {
                                            // manage array parameters
                                            $paramsContent = '';
                                            foreach ($params as $key => $value) {
                                                if (is_array($value)) {
                                                    foreach ($value as $oneArrayValue) {
                                                        $paramsContent .= $key . '[]=' . urlencode($oneArrayValue) . '&';
                                                    }
                                                    unset($params[$key]);
                                                }
                                            }
                                            $paramsContent .= http_build_query($params);
                                            $url .= '?' . $paramsContent;
                                        }
                                    } else {
                                        if (count($params) > 0) {
                                            $options['body'] = json_encode($params);
                                        }
                                    }
                        
                                    if ($method === 'GET') {
                                        $response = $this->client->get($url, $options);
                                    } else if ($method === 'POST') {
                                        $response = $this->client->post($url, $options);
                                    } else if ($method === 'PUT') {
                                        $response = $this->client->put($url, $options);
                                    } else if ($method === 'DELETE') {
                                        $response = $this->client->delete($url, $options);
                                    } else {
                                        return ['error' => $this->l10n->t('Bad HTTP method')];
                                    }
                                    $body = $response->getBody();
                                    $respCode = $response->getStatusCode();
                        
                                    if ($respCode >= 400) {
                                        return ['error' => $this->l10n->t('Bad credentials')];
                                    } else {
                                        if ($jsonResponse) {
                                            return json_decode($body, true);
                                        } else {
                                            return [
                                                'body' => $body,
                                                'headers' => $response->getHeaders(),
                                            ];
                                        }
                                    }
                                } catch (ClientException $e) {
                                    $response = $e->getResponse();
                                    $responseBody = $response->getBody()->getContents();
                                    try {
                                        $responseBody = json_decode($responseBody, true);
                                    } catch (Exception $e) {
                                    }
                                    $this->logger->warning('OPNsense API client error : ' . $e->getMessage(), [
                                        'app' => Application::APP_ID,
                                        'responseBody' => $responseBody,
                                        'exception' => $e->getMessage(),
                                    ]);
                                    return [
                                        'error' => $this->l10n->t('OPNsense API client error'),
                                        'responseBody' => $responseBody,
                                        'exception' => $e->getMessage(),
                                    ];
                                } catch (ServerException $e) {
                                    $response = $e->getResponse();
                                    $responseBody = $response->getBody();
                                    $this->logger->debug('OPNsense API server error : ' . $e->getMessage(), [
                                        'app' => Application::APP_ID,
                                        'responseBody' => $responseBody,
                                        'exception' => $e->getMessage(),
                                    ]);
                                    return [
                                        'error' => 'OPNsense API server error',
                                        'responseBody' => $responseBody,
                                        'exception' => $e->getMessage(),
                                    ];
                                }
                             }
    public function getMenuTree(string $userId): array{
        return $this->restRequest($userId,'api/core/menu/tree');
    }
    //Follow this template to iintroduce new API Request
}