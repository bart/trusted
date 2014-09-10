<?php

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'trusted:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup the trusted application';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Create certs dir if it doesn't exist
		if(!File::isDirectory(base_path().'/certs'))
			File::makeDirectory(base_path().'/certs');

		// Touch sqlite database file
		if(!File::exists(app_path().'/database/trusted.sqlite'))
			File::put(app_path().'/database/trusted.sqlite', '');

		// Setup key and database migration
		if(Config::get('app.key') == 'YourSecretKey!!!') {
			$this->call('key:generate');
			$this->call('migrate', ['--seed' => true, '--force' => true]);
		}
	}

}
