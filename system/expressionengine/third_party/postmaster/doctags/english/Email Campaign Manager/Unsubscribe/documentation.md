### Overview

This tag allows you to directly unsubscribe members to a specific list and service without submitting a form. (Of course you *can* use it in conjunction with a form post, but it's not required.)


### Parameters

service*
:	The email subscription service of your choice. Currently MailChimp and CampaignMonitor are supported.

api_key*
:	The API key of the account to add the subscribers.

list*
:	The unique ID of the list in which to add your subscribers.

email
:	The email used to subscribe to the list;

email_type
:	Preference for the type of email (html, text, or mobile defaults to html)

success_return
:	The return URL if the request succeeds.

failed_return
:	The return URL if the request fails.

_*Required parameters_


### Variables

success
:	Returns `TRUE` if the process succeeds, and `FALSE` is the process fails.

data
:	A variable pair that contains the specific request returned by the email service.

errors
:	A variable pair that can contain one or more errors. These errors are triggered as a result of invalid information being passed to the email service.


### Third-Party API Reference

- [MailChimp](http://apidocs.mailchimp.com/api/1.3/listunsubscribe.func.php)
- [CampaignMonitor](http://www.campaignmonitor.com/api/subscribers/#unsubscribing_a_subscriber)