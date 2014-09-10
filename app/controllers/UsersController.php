<?php

class UsersController extends \BaseController {

	public function __construct() {
		$this->certDir = base_path() . '/certs/';
	}

	public function index() {
		$users = User::all();

		return View::make('users')->withUsers($users);
	}

	public function store() {
		$input = Input::all();
		$rules = [
			'username' => 'required|unique:users',
			'password' => 'required',
			'domains' => 'required',
		];

		$v = Validator::make($input, $rules);

		if($v->fails())
			return Redirect::route('users-path')->withInput()->with('error', 'Please fill in all fields.');

		if($input['domains'][0] != '*')
			return Redirect::route('users-path')->withInput()->with('error', 'Domain has to be a wildcard domain.');

		$domains = $this->getArrayFromString($input['domains']);

		User::create([
			'username' => $input['username'],
			'password' => Hash::make($input['password']),
			'domains' => $domains,
			'is_admin' => Input::exists('isAdmin'),
		]);

		return Redirect::route('users-path')->with('success', 'User has been created.');
	}

	public function destroy($userId) {
		$user = User::find($userId);

		if(!$user)
			return Redirect::route('users-path');

		if(Auth::user()->id == $userId)
			return Redirect::route('users-path')->with('error', 'You can\'t remove yourself.');

		$certsByUser = $user->certs;
		foreach($certsByUser as $cert) {
			$sluggedDomain = Str::slug(str_replace('.', '-', $cert->domain));

			File::delete($this->certDir.$sluggedDomain.'.key');
			File::delete($this->certDir.$sluggedDomain.'.pem');
			File::delete($this->certDir.$sluggedDomain.'.crt');
		}

		$user->delete();

		return Redirect::route('users-path')->with('success', 'User has been deleted.');
	}

	public function show($userId) {
		$user = User::find($userId);

		if(!$user)
			return Response::json([
				'username' => '',
				'domains' => [],
				'isAdmin' => false,
			]);

		return Response::json([
			'username' => $user->username,
			'domains' => $user->domains,
			'isAdmin' => $user->isAdmin(),
		]);
	}

	public function edit() {
		$input = Input::all();
		$rules = [
			'edit-domains' => 'required',
		];

		$v = Validator::make($input, $rules);

		if($v->fails())
			return Redirect::route('users-path')->with('error', 'Please fill in a domain.');

		$user = User::find($input['edit-userId']);

		if(!empty($input['edit-password']))
			$user->password = Hash::make($input['edit-password']);
		$user->domains = $this->getArrayFromString($input['edit-domains']);
		if($user->id != Auth::user()->id || Input::exists('edit-isAdmin'))
			$user->is_admin = Input::exists('edit-isAdmin');
		$user->save();

		return Redirect::route('users-path')->with('success', 'User has been updated.');
	}

	private function getArrayFromString($string) {
		$data = [];
		$explodedString = explode(',', $string);
		foreach($explodedString as $item) {
			if(strpos($item, '*') !== false)
				$data[] = str_replace(' ', '', $item);
		}

		return $data;
	}

}