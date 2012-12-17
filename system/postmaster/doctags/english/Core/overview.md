# Postmaster Overview

### What is Postmaster?

Postmaster is a collection of robust tags, utilities, and API's that enable you and your clients to send email for just about anything in ExpressionEngine. Email can be sent using just about any email service at any time. Postmaster is perfect for end users needed a ready made solution, and developers looking for the most abstract and extendible API.

### What is a parcel?

A parcel is a special template that gets parsed when a channel entry is created or edited. Parcels are parsed and then sent to a specified email or list of emails. Parcels have their own set of templates tags in addition to all the standard template tags and modules provided by ExpressionEngine. A parcel has the ability to impose a very fine level of control over when these emails are sent and to whom they are sent.

### What is a hook?

Hooks were introduced to Postmaster v1.2 and aer really the missing link to making to a complete application with notifications for any event. Hooks are a native to ExpressionEngine, and a way for developers to add "insertion points" for other developers to execute custom logic and change the core functionality. In the case of Postmaster, hooks can be used to send email and pass custom variables to the email parser. Every hook in ExpressionEngine, first or third-party, is included by default. Advanced integration is possible by extending the core Base_hook class.

### How does it work?

A parcel works by defining the sender and recipient(s) of the email along with a channel. Once a channel has been specified you can choose to send emails based on member groups, statuses, categories, and conditionals using channel fields. Then when entries are submitted that match the defined criteria, the email is sent.

Hooks work a little differently. First you check to see if the hook you are seeking has support within Postmaster. If so, defer to hook's documentation for more information on the available variables. If the hook is not available, you can use the API to create your own. Once you create a hook, it can be reused indefinitely. Postmaster hooks simply reuse the native hooks, but use a robust API to make things extremely quick when developing new compatibility.

### How are emails sent?

Emails are sent by default using ExpressionEngine's native libraries in conjunction with your PHP server. However, Postmaster makes no assumption on which service you can use. MailChimp, CampaignMonitor, SendGrid, Postmark, and PostageApp are all currently supported.

*We are constantly adding support for new services. If you have an email service you would like to see supported, [contact us](mailto:support@objectivehtml.com) and let us know which service interests you.*


### Features

- Send transactional and subscription based emails
- Send post-dated and re-occurring emails
- Preview parcels in real-time without refreshing or sending test emails 
- Many full featured developer API's
- Send email with any hook at anytime
- Constantly growing library of supported hooks.