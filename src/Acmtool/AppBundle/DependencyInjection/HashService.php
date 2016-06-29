<?php

namespace Acmtool\AppBundle\DependencyInjection;
class HashService
{
	private $crfProvider;
	public function __construct($crfProvider) {
		$this->crfProvider=$crfProvider;
	}
	public function createHashCode($identifier)
	{
		$random=$identifier.$this->random_string(14);
		$csrfToken = $this->crfProvider->generateCsrfToken($random);
        return $csrfToken;
	}
	private function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}
}