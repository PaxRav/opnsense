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
        $jsonresponse = $this->opnsenseAPIService->getMenuTree($this->userId);
        if (isset($jsonresponse['error'])) {
			return new DataResponse($result, Http::STATUS_BAD_REQUEST);
		} else {
			$menuID = array_map(function ($item) {
				return $item['Id'];
			}, $jsonresponse);
			$menuOrder = array_map(function ($item) {
				return $item['Order'];
			}, $jsonresponse);
			$childrenId = array_map(function ($item) {
				return array_map(function ($child) {
					return $child['Id'];
				}, $item['Children']);
			}, $jsonresponse);
			$response = array(
				'menuID' => $menuID,
				'menuOrder' => $menuOrder,
				'ChildrenId' => $childrenId,
			);
	
			return new DataResponse($response);
		}
    }
}