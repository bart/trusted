<?php

class Cert extends \Eloquent {
	protected $fillable = ['user_id', 'domain', 'csr'];

	public function owner() {
		return $this->belongsTo('User', 'user_id');
	}
}