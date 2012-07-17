## Getting Started

The purpose of this tutorial is to get you familiar with what a parcel is and how it can help you and your clients. This document will dissect each component of a parcel and describe what it does and how it can help.


### Step 1. Overview

The first screen is the overview that outlines which parcels and utilities are installed. For the sake of this tutorial, do not concern yourself with the *Utilities* section. To get started click the 'Create New Parcel' button in the upper right hand corner.

![fullscreen]({THEME_URL}third_party/postmaster/images/docs/parcel-1.jpg)

### Step 2. Defining the Sender/Recipient

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-2.jpg)

After you clicked the 'Create New Parcel', you should define who the email is being sent to, and who it is from. You can use dynamic and/or values to populate these fields.

In additional to all channel meta data as well as your channel fields. You also get access to member data using the 'member:' prefix.


### Step 3. Defining a channel

The next step is to define a channel. This will limit the parcel from firing for only entries in the specified channel. As you change the channel dropdown, the other options will also change to correspond with the selected channel. For instance, only statuses, categories, and member groups will display that are assigned to the selected channel. If you don't see an item on the list, be sure to check that the channel preferences are configured correctly.

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-3.jpg)

#### Configuration

Channel
:	Selecting a channel is required. This will be basis of all sequential configurations. So if you want to send an email for a contact form, select your 'Contact' channel.

Entry Trigger
:	The entry trigger determines when to send the email. If both 'New' and 'Edit' are selected, then the email will be sent for both new and editted entries (provided the other conditions are met).

Categories
:	You can select categories which will be used to send the email. If the entry matches any of the categories that are checked, then the email will be sent (provided the other conditions are met).

Statuses
:	You can use different statuses to determine when emails should be sent. If the entry belongs to any of the checked statuses, then the email will be sent (provided the other conditions are met).

Member Group
:	You can define members groups to determine when emails should be sent. If the author if the entry belong to any of the checked member groups, then the email will be sent (provided the other conditions are met).


### Step 4. Post dated emails and re-occurring emails

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-4.jpg)

This step is totally optional, but it allows you post date emails using specific and/or relative dates. Dates can be static or dynamic.

#### Configuration

Post Date Specific
:	This allows you define a specific date in which to send a post-dated email. The date can be dynamic or static, but must be a specific date.

Post Date Relative
:	This allows you to define a human readable string of text to denote the length of time in the future an email should be sent. Example, "+1 day", "+1 hour", "+1 week".

Send Every
:	This allows you to define a length of time to send a reoccuring emails. This should be a relative date similar to the "Post Date Relative" setting.


### Step 5. Custom conditionals

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-5.jpg)

This step is also optional, but allows you to define a proprietary conditional that will determine of an email should be fired. For instance, you can only send emails if a particular channel field contained specific values. Simply create your own conditional and output TRUE to send the email, and FALSE to prevent it from sending. If no value is present the field is ignored.


### Step 6. Parcel preview

![fullscreen]({THEME_URL}third_party/postmaster/images/docs/parcel-6.jpg)

*If you just created a channel and are trying to setup a new Parcel, be sure to have some sample data at your disposal, it makes creating parcels a lot easier. If you have an empty channel, before continuing be sure to create a test entry before creating your template.*

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-7.jpg)

#### Parse Entry Dropdown

Once you have some sample data to work right, it will appear in the *Parse Entry* dropdown. You can select different options (entries) from this list to parse your template using various entries to test all the different scenarious. Each time you change the dropdown, the preview will update and re-render itself.

While this feature isn't required, it's highly recommended - it will save you a lot of time in the long run.


### Step 7. Template Editor

![fullscreen]({THEME_URL}third_party/postmaster/images/docs/parcel-8.jpg)

We spent a lot of time on this interface, and we wanted to make sure it wasn't just functional, but would save developers a lot of time. We also wanted to try to eliminate some of the pitfalls that are associated with creating email templates. All of your HTML, CSS, and EE tags will be rendered as you type. You will be able to fix coding errors on the fly, and quickly parse your data against multiple entries.

#### Features

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-9.jpg)

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-10.jpg)

Syntax Highlighting
:	You can easily choose your own color schema that fits your coding style. This will improve your reading comprehension, thus making the process that much easier and faster. No more dreading creating email templates and figuring out how you are going to send them.

Refresh Preview
:	You can refresh the preview and rerender your code at any time by clicking the 'Refresh Preview' link. By default, the code will re-render itself after a define length of time after typing. This preview rate can be adjusted to your preference.

Variable Reference
:	Gone are the days when you need to open up the channel fields screen just to remember your naming convention. Simply open the tab and a panel will display with all your channel fields. Clicking on them will output the tag on the cursor location in the template editor. Click the toggle will either add or remove the curly braces from the output.


### Step 8. Subject

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-11.jpg)

This step is pretty straight forward, just type your subject as desired. All the same template tags are available here, just as they are in the template editor. If statements, date formatting, custom fields &dash; it's all accepted and parsed accordingly.

### Step 9. Email Services

![float-right]({THEME_URL}third_party/postmaster/images/docs/parcel-12.jpg)

The final step is selecting which service is going to send your email. Each email service can fundementally change the way a parcel works. With that said, all the rules that are outlined in this documentat are the default. Since Postmaster is so versatile and API driven, things can be changed as the developer desires.

For instance, sending transaction email using ExpressionEngine's email service utilizes all the to, cc, and bcc fields. Where as MailChimp and CampaignMonitor ignore these fields entirely and send emails to entire lists of people that have previously subscribed. 

### Developer API's

You can take things even further with the developer API's. Documentation on this is coming soon.