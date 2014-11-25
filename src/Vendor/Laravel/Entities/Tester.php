<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Tester extends Model {

	protected $fillable = [
		'name',
		'command',
		'output_folder',
		'output_html_fail_extension',
		'output_png_fail_extension',
	];

}
