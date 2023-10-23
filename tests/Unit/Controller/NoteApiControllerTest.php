<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Diego Munoz <dmuno072@fiu.edu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\OPNsense\Tests\Unit\Controller;

use OCA\OPNsense\Controller\NoteApiController;

class NoteApiControllerTest extends NoteControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new NoteApiController($this->request, $this->service, $this->userId);
	}
}
