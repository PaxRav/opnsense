<?php


namespace OCA\OPNsense\Controller;

use Exception;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\OPNsense\Service\OPNsenseAPIService;
use OCA\OPNsense\AppInfo\Application;
use OCP\IURLGenerator;


class OpnsenseAPIController extends Controller {

    private IConfig $config;
	private OPNsenseAPIService $opnsenseAPIService;
	private ?string $userId;
	private IURLGenerator $urlGenerator;


    public function __construct(string $appName,
								IRequest $request,
								IConfig $config,
								OPNsenseAPIService $opnsenseAPIService,
								IURLGenerator $urlGenerator,
								?string $userId) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->opnsenseAPIService = $opnsenseAPIService;
		$this->userId = $userId;
		$this->urlGenerator = $urlGenerator;
	}

    /**
     * 
     */
    public function getTreeInfo(string $userId): DataResponse{
        $jsonfile = $this->opnsenseAPIService->getMenuTree($this->userId);
        if (isset($jsonfile['error'])) {
			return new DataResponse($result, Http::STATUS_BAD_REQUEST);
		} else {
			return new DataResponse($result);
		}
    }
}