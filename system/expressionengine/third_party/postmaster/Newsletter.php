<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$doctags['postmaster']['newsletter'] = array(
	
	/*------------------------------------------
	 *	Newsletter Subscription
	/* -------------------------------------- */
	
	array(
		'name'        => 'newsletter',
		'title'       => 'Newsletter Subscription',
		'description' => 'Some description',
		'return_type' => 'string',
		'is_looping'  => FALSE,
		'snippets'	  => array(
			array(
				'name'        => 'postmaster_subscribe_mailchimp',
				'value'       => 'test',
				'description' => 'Some Description'
			),
			array(
				'name'        => 'postmaster_subscribe_mailchimp',
				'value'       => 'test',
				'description' => 'Some Description'
			),
			array(
				'name'        => 'postmaster_subscribe_mailchimp',
				'value'       => 'test',
				'description' => 'Some Description'
			),
			array(
				'name'        => 'postmaster_subscribe_mailchimp',
				'value'       => 'test',
				'description' => 'Some Description'
			)
		),
		'parameters' => array(
			array(
				'name'          => 'api_key',
				'description'   => 'Some Description',
				'default_value' => NULL,
				'type'          => 'string'
			),
			array(
				'name'          => 'list_id',
				'description'   => 'Some Description',
				'default_value' => NULL,
				'type'          => 'string'
			)
		),
		'examples' => array(
			array(
				'name'        => 'Example 1',
				'description' => 'Some Description',
				'code'        => '{some code goes here}'
			),
			array(
				'name'        => 'Example 1',
				'description' => 'Some Description',
				'code'        => '{some code goes here}'
			),
			array(
				'name'        => 'Example 1',
				'description' => 'Some Description',
				'code'        => '{some code goes here}'
			)
		),
		'variables' => array(
			array(
				'name'        => 'some_var',
				'description' => 'Some Var Description',
				'code'        => '{some_var}'
			),
			array(
				'name'        => 'some_var_2',
				'description' => 'Some Var Description',
				'code'        => '{some_var_2}'
			),			
			array(
				'name'        => 'some_var_3',
				'description' => 'Some Var Description',
				'code'        => '{some_var_3}'
			)
		),
		'tag_pairs' => array(
			array(
				'name'        => 'tag_pair',
				'is_looping'  => TRUE,
				'description' => 'Some Tag Pair Description',
				'variables' => array(
					array(
						'name'        => 'some_var',
						'description' => 'Some Var Description',
						'code'        => '{some_var}'
					),
					array(
						'name'        => 'some_var_2',
						'description' => 'Some Var Description',
						'code'        => '{some_var_2}'
					),			
					array(
						'name'        => 'some_var_3',
						'description' => 'Some Var Description',
						'code'        => '{some_var_3}'
					)
				),
				'examples' => array(
					array(
						'name' => 'Basic Usage',
						'description' => 'Some text',
						'code' => '
							{some_var}
								{var1}
								{var2}
								{var3}
							{/some_var}
						'
					)
				)
			)
		)
	)
);