<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	use UserTrait;

	protected $hidden = ['password', 'remember_token'];
	protected $fillable = ['username', 'password', 'domains', 'is_admin'];

	public function getDomainsAttribute($value) {
		return json_decode($value);
	}

	public function setDomainsAttribute($value) {
		if(is_array($value))
			$value = json_encode($value);

		$this->attributes['domains'] = $value;
	}

	public function getDomainsCommaSeparated() {
		$domains = null;
		foreach($this->domains as $domain) {
			$domains .= $domain . ', ';
		}

		return substr($domains, 0, -2);
	}

	public function isAdmin() {
		return (bool) $this->is_admin;
	}

	public function certs() {
		return $this->hasMany('Cert');
	}

	public function getDomainsWithoutAsterisk() {
		$domains = [];
		foreach($this->domains as $domain) {
			$domainWithoutAsterisk = substr($domain, 1);
			$domains[$domainWithoutAsterisk] = $domainWithoutAsterisk;
		}

		return $domains;
	}

}
