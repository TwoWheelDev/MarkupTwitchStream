# ProcessWire Twitch Stream Markup


![StyleCI Badge](https://github.styleci.io/repos/970039020/shield?branch=main) ![GitHub License](https://img.shields.io/github/license/twowheeldev/MarkupTwitchStream?style=flat-square)

A custom Fieldtype and Inputfield for ProcessWire that allows you to store a Twitch username on a page and view its live stream status.

The module currently uses TailwindCSS for styling (but future versions may make the styles configurable).

## Features

- Stores a Twitch username in a field.
- Shows whether the user is currently live from the page editor.
- Uses Processwire cache for storage of Twitch OAuth Token.

## Modules Included

- `FieldtypeTwitch`: Stores the Twitch username.
- `InputfieldTwitch`: Input field with live status preview in admin.
- `MarkupTwitchStream`: For rendering Twitch information on the frontend.

## Screeenshots
![Screenshot - Online Light](https://github.com/TwoWheelDev/MarkupTwitchStream/raw/main/screenshots/Online-Light.png) ![Screenshot - Online Dark](https://github.com/TwoWheelDev/MarkupTwitchStream/raw/main/screenshots/Online-Dark.png)
![Screenshot - Offline Light](https://github.com/TwoWheelDev/MarkupTwitchStream/raw/main/screenshots/Offline-Light.png) ![Screenshot - Offline Dark](https://github.com/TwoWheelDev/MarkupTwitchStream/raw/main/screenshots/Offline-Dark.png)

---

## Installation

1. Copy the module files into a folder named `MarkupTwitchStream/` under `/site/modules/`.
2. Go to **Modules > Refresh** in the ProcessWire admin.
3. Install the `MarkupTwitchStream` module, this will also install:
   - `InputfieldTwitch`
   - `FieldtypeTwitch` (this will auto-install `InputfieldTwitch`)

---

## Setup

1. Go to **Setup > Fields**, and create a new field using the **Twitch** fieldtype.
2. Add the field to a template (e.g., `profile`, `streamer`, etc.).
3. Edit a page using that template and enter the Twitch username.
4. Once saved, the field will show the user's live status directly in the admin UI.

---

## Frontend

The render function takes two parameters, the current page and the name of the field containing the Twitch username

```php
/** @var TwitchStream $twitch */
$twitch = $modules->get('MarkupTwitchStream');
$twitch->render($page, "twitch");
```

---

## Twitch API Credentials

You need a [Twitch developer application](https://dev.twitch.tv/console/apps) to use the API.

### Steps:

1. Create a new app on the Twitch Developer Console.
2. Set the OAuth Redirect URL (you won’t use it for this, but Twitch requires one).
3. Copy your **Client ID** and **Client Secret**.
4. Add them to the `MarkupTwitchStream` module configuration
