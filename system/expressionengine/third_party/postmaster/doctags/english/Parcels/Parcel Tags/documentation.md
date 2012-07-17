## Parcel Tags

Parcels tags are created by Postmaster to make it easier to create your templates by giving you direct access to the channel fields. 

*If your channel fields require a channel entries hook, using them directly will not work and could potentially cause an error. So, if your channel field doesn't work as expected, simply use a channel entries loop to fix the problem.*


### Tag Reference

{parcel:title}
:	The title of the entry.

{parcel:url_title}
:	The url_title of the entry.

{parcel:entry_id}
:	The entry_id of the entry.

{parcel:channel_id}
:	The channel_id of the entry.

{parcel:author_id}
:	The author_id of the entry.

{parcel:status}
:	The status of the entry.

{parcel:entry_date}
:	The entry_date of the entry. Can be formatted like a standard EE date field.

{parcel:expiration_date}
:	The expiration_date of the entry. Can be formatted like a standard EE date field.

{parcel:your_channel_field}
:	You can access you channel fields for each entry

{member:member_id}
:	The member_id of the author of the entry.

{member:group_id}
:	The group_id of the author of the entry.

{member:email}
:	The email of the author of the entry.

{member:username}
:	The username of the author of the entry

{member:screen_name}
:	The screename of the author of the entry

{member:your_member_field}
:	You can access member custom fields too


### Example of channel entries usage
~~~

{parcel:title}
{parcel:your_text_field}

{exp:channel:entries channel="your-channel" entry_id="{parcel:entry_id}"}
	
	{parcel:entry_date format="%Y"}
	{parcel:your_text_field2}
	
	{your_matrix_field}
		{col1} {col2}	
	{/your_matrix_field}
{/exp:channel:entries}
~~~
