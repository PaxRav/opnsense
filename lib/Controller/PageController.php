<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Diego Munoz <dmuno072@fiu.edu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\OPNsense\Controller;

use OCA\OPNsense\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use Psr\Log\LoggerInterface;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Util;


class PageController extends Controller {

	private Iconfig $config;
	private IAppManager $appManager;
	private IInitialState $initialStateService;
	private LoggerInterface $logger;
	private ?string $userId;

	public function __construct(string $appName,
								IRequest $request,
								IInitialState $initialStateService,
								Iconfig $config
								?string $userId) {
		parent::__construct($appName, $request);
		$
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
		Util::addScript(Application::APP_ID, 'opnsense-main');

		return new TemplateResponse(Application::APP_ID, 'main',[]);
	}
}
