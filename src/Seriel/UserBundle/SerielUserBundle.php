<?php

namespace Seriel\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SerielUserBundle extends Bundle
{
	public function getParent() {
		return 'FOSUserBundle';
	}
}
