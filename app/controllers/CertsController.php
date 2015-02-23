<?php

use Symfony\Component\Process\Process;

class CertsController extends \BaseController {
	private $certDir;

	public function __construct() {
		$this->certDir = base_path() . '/certs/';
		$this->beforeFilter('rootCAExists', ['except' => ['rootCAIndex', 'rootCACreate']]);
	}

	public function redirect() {
		return Redirect::route('certs-path');
	}

	public function index() {
		if(Auth::user()->isAdmin())
			$certs = Cert::with('owner')->orderBy('domain', 'ASC')->get();
		else
			$certs = Auth::user()->certs;

		return View::make('certs')
			->withCerts($certs);
	}

	public function store() {
		$input = Input::all();
		$rules = [
			'c' => 'required',
			'cn' => 'required',
			'cns' => 'required',
		];

		$v = Validator::make($input, $rules);

		if($v->fails())
			return Redirect::route('certs-path')
				->with('error', 'Please provide at least country and common name.');

		if(!in_array('*'.$input['cns'], Auth::user()->domains))
			return Redirect::route('certs-path')
				->with('error', 'You are not allowed to create a certificate for this domain.');

		$sluggedDomain = Str::slug(str_replace('.', '-', $input['cn'].$input['cns']));
		if(!File::exists($this->certDir . $sluggedDomain . '.crt')) {
			// Get field
			$c = Input::get('c');
			$st = Input::get('st');
			$l = Input::get('l');
			$o = Input::get('o');
			$ou = Input::get('ou');
			$cn = $input['cn'].$input['cns'];
			$email = Input::get('email');

			// Prepare subject
			$subj = "'/C={$c}";
			$subj .= !empty($st) ? "/ST={$st}" : '';
			$subj .= !empty($l) ? "/L={$l}" : '';
			$subj .= !empty($o) ? "/O={$o}" : '';
			$subj .= !empty($ou) ? "/OU={$ou}" : '';
			$subj .= "/CN={$cn}";
			$subj .= !empty($email) ? "/Email={$email}" : '';
			$subj .= "'";

			// Create private key and CSR
			$process = new Process("cd {$this->certDir} && openssl req -nodes -new -keyout {$sluggedDomain}.key -out {$sluggedDomain}.csr -days 365 -subj {$subj}");
			$process->run();

			// Sign cert, convert into DES and remove CSR
			$process = new Process("cd {$this->certDir} && openssl x509 -CA rootCA.pem -CAkey rootCA.key -CAcreateserial -days 365 -req -in {$sluggedDomain}.csr -out {$sluggedDomain}.pem && openssl x509 -in {$sluggedDomain}.pem -out {$sluggedDomain}.crt");
			$process->run();

			File::delete($this->certDir.$sluggedDomain.'.csr');

			// Create database entry
			Cert::create([
				'user_id' => Auth::user()->id,
				'domain' => $cn,
				'csr' => 0,
			]);
		}

		return Redirect::route('certs-path')
			->with('success', 'Certificate has been created.');
	}

	public function sign() {
		$input = Input::all();
		$rules = [
			'csr' => 'required',
		];

		$v = Validator::make($input, $rules);

		if($v->fails())
			return Redirect::route('certs-path')
				->with('error', 'Please select a CSR.');

		// Upload CSR
		$randomFileName = str_random();
		Input::file('csr')->move($this->certDir, $randomFileName.'.csr');

		// Check if CSR is valid and user is allowed to sign requests for the given domain
		$process = new Process("cd {$this->certDir} && LC_ALL=C openssl req -text -noout -in {$randomFileName}.csr | grep Subject | grep -o 'CN=.*,' | cut -c 4- | sed 's/.$//'");
		$process->run();
		$csrDomain = trim($process->getOutput());

		// Calculate domain string to check against users allowed domain pool
		$checkDomain = '*'.substr($csrDomain, strpos($csrDomain, '.'));

		if(!$csrDomain || !in_array($checkDomain, Auth::user()->domains)) {
			File::delete($this->certDir.$randomFileName.'.csr');
			return Redirect::route('certs-path')
				->with('error', 'The provided CSR file is invalid or you are not allowed to create a certificate for the given domain.');
		}

		// Sign CSR
		$sluggedDomain = Str::slug(str_replace('.', '-', $csrDomain));
		$process = new Process("cd {$this->certDir} && openssl x509 -CA rootCA.pem -CAkey rootCA.key -CAcreateserial -days 365 -req -in {$randomFileName}.csr -out {$randomFileName}.pem && openssl x509 -in {$randomFileName}.pem -out {$sluggedDomain}.crt");
		$process->run();

		// Remove CSR and PEM
		File::delete($this->certDir.$randomFileName.'.csr');
		File::delete($this->certDir.$randomFileName.'.pem');

		// Create database entry
		Cert::create([
			'user_id' => Auth::user()->id,
			'domain' => $csrDomain,
			'csr' => 1,
		]);

		// Return signed certificate as download
		return Response::download($this->certDir.$sluggedDomain.'.crt');
	}

	// Todo: Put the following 4 methods into singleton and assign $sluggedDomain in constructor
	public function getCert($certId){
		$cert = Cert::find($certId);

		if(!$cert || ($cert->owner->id != Auth::user()->id && !Auth::user()->isAdmin()))
			return '';

		$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));

		return nl2br(File::get($this->certDir.$sluggedDomain.'.crt'));
	}

	public function downloadCert($certId){
		$cert = Cert::find($certId);

		if(!$cert || ($cert->owner->id != Auth::user()->id && !Auth::user()->isAdmin()))
			return Redirect::route('certs-path');

		$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));

		return Response::download($this->certDir.$sluggedDomain.'.crt');
	}

	public function getKey($certId){
		$cert = Cert::find($certId);

		if(!$cert || ($cert->owner->id != Auth::user()->id && !Auth::user()->isAdmin()))
			return '';

		$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));

		return nl2br(File::get($this->certDir.$sluggedDomain.'.key'));
	}

	public function downloadKey($certId){
		$cert = Cert::find($certId);

		if(!$cert || ($cert->owner->id != Auth::user()->id && !Auth::user()->isAdmin()))
			return Redirect::route('certs-path');

		$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));

		return Response::download($this->certDir.$sluggedDomain.'.key');
	}

	public function destroy($certId) {
		$cert = Cert::find($certId);

		if(!$cert || ($cert->owner->id != Auth::user()->id && !Auth::user()->isAdmin()))
			return Redirect::route('certs-path');

		$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));
		$cert->delete();

		File::delete($this->certDir.$sluggedDomain.'.key');
		File::delete($this->certDir.$sluggedDomain.'.pem');
		File::delete($this->certDir.$sluggedDomain.'.crt');

		return Redirect::route('certs-path');
	}

	public function rootCAIndex() {
		$rootCACertExists = File::exists($this->certDir . 'rootCA.crt');

		if(!$rootCACertExists)
			return View::make('rootCACreate');

		// Get certificate information
		$process = new Process("cd {$this->certDir} && openssl x509 -in rootCA.crt -text -noout");
		$process->run();
		$certInfo = $process->getOutput();

		return View::make('rootCA')
			->withCert($certInfo);
	}

	public function rootCACreate() {
		$input = Input::all();
		$rules = [
			'c' => 'required',
			'cn' => 'required',
			'email' => 'required|email',
		];

		$v = Validator::make($input, $rules);

		if($v->fails())
			return Redirect::route('root-ca-path')
				->with('error', 'Please provide at least country, common name and an email address.');

		if(!File::exists($this->certDir . 'rootCA.crt')) {
			$c = Input::get('c');
			$st = Input::get('st');
			$l = Input::get('l');
			$o = Input::get('o');
			$ou = Input::get('ou');
			$cn = Input::get('cn');
			$email = Input::get('email');
			$process = new Process("cd {$this->certDir} && openssl req -nodes -new -x509 -keyout rootCA.key -out rootCA.pem -days 3650 -subj '/C={$c}/ST={$st}/L={$l}/O={$o}/OU={$ou}/CN={$cn}/Email={$email}'");
			$process->run();

			$process = new Process("cd {$this->certDir} && openssl x509 -in rootCA.pem -out rootCA.crt");
			$process->run();
		}

		return Redirect::route('root-ca-path')
			->with('success', 'Root CA has been created.');
	}

	public function rootCADownloadCert() {
		return Response::download($this->certDir . 'rootCA.crt');
	}

	public function rootCADownloadKey() {
		return Response::download($this->certDir . 'rootCA.key');
	}

	public function rootCARemove() {
		File::delete($this->certDir . 'rootCA.crt');
		File::delete($this->certDir . 'rootCA.key');
		File::delete($this->certDir . 'rootCA.pem');
		File::delete($this->certDir . 'rootCA.srl');

		return Redirect::route('root-ca-path')
			->with('success', 'Root CA has been deleted.');
	}

}
