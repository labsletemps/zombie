<?php

namespace Seriel\UserBundle\Component;

use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

class SerielUsernamePasswordFormAuthenticationListener extends UsernamePasswordFormAuthenticationListener {

	protected function attemptAuthentication(Request $request) {
		$hash = $request->request->get('_hash');
		
		if ($hash) {
			$request->getSession()->set('_hash_seriel', $hash);
		}
		
		return parent::attemptAuthentication($request);
	}
}