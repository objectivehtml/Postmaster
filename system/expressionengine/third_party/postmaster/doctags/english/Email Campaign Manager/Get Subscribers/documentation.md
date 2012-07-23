### Overview

Get subscribers is a method that will retrieve a list of subscribers from a specified service and list.


### Parameters

service*
:	The email subscription service of your choice. Currently MailChimp and CampaignMonitor are supported.

api_key*
:	The API key of the account to add the subscribers.

list*
:	The unique ID of the list in which to add your subscribers.

order_by
:	(CampaignMonitor only) Order the results by a specific field <em>email, date, or name</em>.

sort
:	(CampaignMonitor only) Order the results <em>asc</em> or <em>desc</em>.

status
:	Get the members of the list by a given status.

start
:	Page number used to the display and paginate results.

limit
:	Limit the number of subscribers displayed in a page.

prefix
:	A string that is used to prefix all the variables to avoid naming conflicts.

_*Required parameters_


### Variables

The variables returned are dependent on the service. If no service is specified, the variable is universal.

subscriber:index
:	The index of the current row in the loop

subscriber:count

:	The count of the current row in the loop

subscriber:total
:	The total number of subscribers	

subscriber:email
:	The subscriber or unsubscriber email address.

subscriber:data
:	The variable pair that contains the service specific data returned by the request

	1. **subscriber:reason** - *MailChimp Only* For unsubscribers only, this is the reason collected for unsubscribing. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'.
	2. **subscriber:reason_text** - *MailChimp Only* For unsubscribers only, this is the text entered if 'other'.
	
	
### Third-party API Reference

- [MailChimp](http://apidocs.mailchimp.com/api/1.3/listmembers.func.php)
- [CampaignMonitor](http://www.campaignmonitor.com/api/lists/#active_subscribers)
