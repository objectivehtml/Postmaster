## Email Campaign Manager

### Problem

Email campaign integration is a common request among clients. There are a lot of different add-ons that do various things, all in their own unique ways. This forces people into a box. Clients that use one add-ons (or service) may get things that aren't provided in another. More add-ons means more code to support and more things that are likely to fail when you upgrade.

### Solution

Postmaster provides a suite of utilities that help you and your client manage their email lists that isn't dependent on any one service. Postmaster takes a different approach by basing everything on an API that standardizes these requests. You can use the same tag to subscribe users to MailChimp and CampaignMonitor.

### Features

1. Compatible with MailChimp and CampaignMonitor
2. Standardized and memorable API
3. Stand-alone entry forms (SAEF) with inline error handling
4. Easy to implement with AJAX

*We are constantly adding features and compatibility with new services. If the email service you use isn't available, let us know. We implement services based on popular demand.*

### Supported Services

- [MailChimp](http://www.mailchimp.com)
- [CampaignMonitor](http://www.campaignmonitor.com)

### Syntax

You'll notice that the campaign tags have 4 segments on EE 2.5+ and 5 segments on older versions. Don't let this confuse you, these are just like any other tag with 2 or 3 segments. Since Postmaster is completely extendible, thus developers can create new classes with custom methods that do whatever the developer wants.

*Obviously the examples below are referring to a single tag. Use the same logic and apply it to the tags that apply to you.*

####(*EE 2.5+*) - `{exp:postmaster:campaign:subscribe}`

Segment 1 - exp
:	This segment is standard and is required.

Segment 2 - postmaster
:	This segment is also required and initiates the Postmaster module.

Segment 3 - campaign
:	This segment load a utility class

Segment 4 - subscribe
:	This segment executes a specific method from the defined class.

#### (*EE 2.4 and older*) - `{exp:postmaster:delegate:campaign:subscribe}`

Segment 1 - exp
:	This segment is standard and is required.

Segment 2 - postmaster
:	This segment is also required and initiates the Postmaster module.

Segment 3 - delegate
:	This segment is only required for EE 2.4 and older.

Segment 4 - campaign
:	This segment load a utility class

Segment 5 - subscribe
:	This segment executes a specific method from the defined class.

