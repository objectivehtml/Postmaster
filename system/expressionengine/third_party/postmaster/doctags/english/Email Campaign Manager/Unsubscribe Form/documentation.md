### Overview

This tag allows you create a stand-alone entry form (SAEF) that will unsubscribe a member from a specified list and service once submitted. This tag handles all errors and will redirect the user upon completion.


### Parameters

service*
:	The email subscription service of your choice. Currently MailChimp and CampaignMonitor are supported.

api_key*
:	The API key of the account to add the subscribers.

list*
:	The unique ID of the list in which to add your subscribers.

return
:	The return URL if the request succeeds. If the request fails errors will be displayed.

email_type
:	Preference for the type of email (html, text, or mobile defaults to html)

action
:	Override the form action to something other than default.

secure_action
:	Force HTTPS for the form's action.

secure_return
:	Force HTTPS for the return URL.

_*Required parameters_


### Variables

Post Variables
:	A form variable can be accessed using the post: prefix.
	
	`{post:your_var_name}`

field_errors
:	A variable pair that can contain one or more errors. There errors are returned before the email service is contacted. Field errors are usually a result of improperly formatted data. *error_handling must be set to 'inline' to access these variables*
	
	~~~~
	{field_errors}
		{error}
	{/field_errors}
	~~~~
	
global_errors
:	A variable pair that can contain one or more errors. There errors are returned after the email service is contacted. Global are triggered as a result of invalid data getting sent to the email service. *error_handling must be set to 'inline' to access these variables*

	~~~~
	{global_errors}
		{error}
	{/globak_errors}
	~~~~


### Third-Party Reference

- [MailChimp](http://apidocs.mailchimp.com/api/1.3/listunsubscribe.func.php)
- [CampaignMonitor](http://www.campaignmonitor.com/api/subscribers/#unsubscribing_a_subscriber)
