<?php

class UserTableSeeder extends Seeder {

	public function run()
	{
		User::create([
			'username' => 'admin',
			'password' => Hash::make('password'),
			'domains' => ['*.yourdomain.com'],
			'is_admin' => true,
		]);
	}

}