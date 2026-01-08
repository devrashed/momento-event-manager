# Ultimate Events Manager

A comprehensive WordPress event management plugin with WooCommerce and non-WooCommerce registration options.

## Features

### Custom Post Types
- **Events**: Main event post type with full event details
- **Organizers**: Manage event organizers
- **Volunteers**: Manage event volunteers
- **Sponsors**: Manage event sponsors
- **Event Registrations**: Store registration data (for non-WooCommerce mode)

### Event Management
- Multiple ticket types per event
- Event date/time management
- Event location and address
- Link multiple organizers, volunteers, and sponsors to events
- Featured images support

### Registration Methods

#### WooCommerce Registration
- Automatic cart clearing when viewing an event
- All ticket types added to cart by default (quantity 1)
- AJAX cart updates when ticket quantities change
- Dynamic attendee details form (one per ticket)
- Attendee data stored in WooCommerce orders
- Full WooCommerce checkout integration

#### Simple Registration (Non-WooCommerce)
- Simple registration form with name, phone, email, address
- Multiple ticket selection
- Dynamic attendee details form
- Registration data stored as custom post type
- Thank you page with registration summary

### Settings
- Choose between WooCommerce or Simple registration method
- Settings page accessible from Events menu

## Installation

1. Upload the plugin files to `/wp-content/plugins/ultimate-events-manager/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Events > Settings to configure registration method
4. If using WooCommerce, ensure WooCommerce is installed and activated

## Usage

### Creating Events

1. Go to **Events > Add New**
2. Fill in event details (title, description, featured image)
3. Set event date, time, location, and address in the Event Details meta box
4. Add ticket types in the Ticket Types meta box
5. Select organizers, volunteers, and sponsors from the respective meta boxes
6. Publish the event

### Creating Organizers, Volunteers, and Sponsors

1. Go to **Events > Organizers** (or Volunteers/Sponsors)
2. Add new items with title, description, and featured image
3. These can then be linked to events

### Registration Process

#### With WooCommerce
1. Visit a single event page
2. Cart is automatically cleared and tickets are added
3. Adjust ticket quantities (updates cart via AJAX)
4. Fill in attendee details (one form per ticket)
5. Complete WooCommerce checkout
6. Attendee data is saved with the order

#### Without WooCommerce
1. Visit a single event page
2. Fill in registration details
3. Select ticket quantities
4. Fill in attendee details (one form per ticket)
5. Submit registration
6. View thank you page with registration summary

## File Structure

```
ultimate-events-manager/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── class-uem-ajax.php
│   ├── class-uem-meta-boxes.php
│   ├── class-uem-post-types.php
│   ├── class-uem-registration.php
│   ├── class-uem-settings.php
│   ├── class-uem-template-loader.php
│   ├── class-uem-woocommerce.php
│   └── uem-template-functions.php
├── templates/
│   ├── single-event.php
│   └── thank-you.php
└── ultimate-events-manager.php
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- WooCommerce (optional, only if using WooCommerce registration method)

## Hooks and Filters

The plugin uses standard WordPress hooks and can be extended using:
- `uem_event_meta_boxes` - Filter event meta boxes
- `uem_registration_data` - Filter registration data before saving
- `uem_attendee_data` - Filter attendee data before saving

## Support

For support, please contact the plugin developer or visit the plugin support page.

## Changelog

### 1.0.0
- Initial release
- Custom post types (Events, Organizers, Volunteers, Sponsors, Registrations)
- WooCommerce integration
- Simple registration system
- Ticket management
- Attendee details collection
- Thank you page

