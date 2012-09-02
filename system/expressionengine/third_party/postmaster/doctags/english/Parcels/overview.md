# Parcel Overview

### What is a parcel?

A parcel is a special template that gets parsed when a channel entry is created or edited. Parcels are parsed and then sent to a specified email or list of emails. Parcels have their own set of templates tags in addition to all the standard template tags and modules provided by ExpressionEngine. A parcel has the ability to impose a very fine level of control over when these emails are sent and to whom they are sent.


### How does it work?

A parcel works by defining the sender and recipient(s) of the email along with a channel. Once a channel has been specified you can choose to send emails based on member groups, statuses, categories, and conditionals using channel fields. Then when entries are submitted that match the defined criteria, the email is sent.


### How are emails sent?

Emails are sent by default using ExpressionEngine's native libraries in conjunction with your PHP server. However, Postmaster makes no assumption on which service you can use. MailChimp, CampaignMonitor, SendGrid, Postmark, and PostageApp are all currently supported.

*We are constantly adding support for new services. If you have an email service you would like to see supported, [contact us](mailto:support@objectivehtml.com) and let us know which service interests you.*


### Features

- Send transactional and subscription based emails
- Send post-dated and re-occurring emails
- Preview parcels in real-time without refreshing or sending test emails 
- Many full featured developer API's