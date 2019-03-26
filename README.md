# app-event
This plugin adds Full REST-API support for Tickera Events Plugin

## Features:
	- Adds rest-api support for Tickera Events CPT
	- Supports Ticket purchase within Event
	- Supports featured image of Event
	- Supports AppPresser.com framework

*Tickera plugin is required for this to work. Please visit https://tickera.com/ for more information.
*Works best with AppPresser: easily create your own WordPress application with no coding experience, visit https://apppresser.com for more information.
	
## How it works - AppPresser
1. Simply add a custom page and choose "WordPress Posts" and the following URL as API endpoint: 
https://yourdomain.com/wp-json/wp/v2/tc_events

For categories, add "event_category=ID" behind the slug. Example:
https://yourdomain.com/wp-json/wp/v2/tc_events?event_category=248

To filter the events based on Event date instead of Publishing date, use this endpoint:
https://yourdomain.com/wp-json/wp/v2/tc_events?filter[meta_key]=event_date_time&order=asc 
Or use "desc" at the end to filter events on descending date.

To filter on category and Event date, use the endpoint like this:
https://yourdomain.com/wp-json/wp/v2/tc_events?event_category=248&filter[meta_key]=event_date_time&order=asc


2. Copy the custom CSS from the 'public' folder inside this plugin and paste it into the 'Custom CSS' section in MyApppresser > Colors. Change the colors of the button and icons to match your theme.

3. You are done. Enjoy the blazing fast experience of viewing/searching events and buying tickets!
	
## Support
This plugin works out of the box and should works best with AppPresser framework or Ionic Framework. Should you stumble on issues/bugs, please open a support ticket <a href="https://github.com/RoadmapStudios/app-event/issues">here</a> .
