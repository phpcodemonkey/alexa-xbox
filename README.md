alexa-xbox
=========================

This is an application for use with Amazon Alexa in order to turn your Xbox on using your Amazon Echo or Echo Dot.

Install
-----

When setting up an Alexa app, you can specify a secure URL where these files should be placed. The only file you will need to edit is to copy `site/includes/.env-dist` to your own file `site/includes/.env` file.

```ini
APP_ID=amzn1.ask.skill.12345678-1234-1234-1234-123456789123
APP_NAME="Xbox Control"

IP_ADDRESS=123.456.654.321 # Current public IP address
XBOX_LIVE_ID=ABCD1234ABCD1234 # Xbox -> Settings -> System -> Console Info -> Xbox Live device ID
```

Additionally, you will need to ensure the `site/logs` folder is writable by your web server.
If you wish to have Slack integration, you can edit the `.env` entries with the relevant details:

```ini
SLACK_API_TOKEN=""
SLACK_CHANNEL="#log_alexa_xbox"
```

Finally, you'll need to have [composer](https://getcomposer.org/download/), the common PHP package manager, available and to run `composer install` once installed. Completing this will bring in the dependent libraries to `site/vendor`.

IP Address & Xbox Live device ID
-----

For the app to connect to your Xbox, 3 things are required:

1. Your router must forward port 5050 to your Xbox.
2. As your Xbox will be turned on remotely, the public IP address for your Xbox is needed.
3. The Xbox Live device ID. On your Xbox: All settings > System > Console Info > Xbox Live device ID.
 
Testing
-----

It's probably a good idea to test the connection first, between the server that runs the app and your Xbox. To do this, you can simply comment out the $Alexa->auth() statement as requests from Alexa will only ever pass this check.

How it works
-----

The first thing the app does, is check that the Xbox is not already running by pinging it. If it isn't, then it will attempt to send a magic packet to the Xbox. It will wait 1 second before pinging again to see if the packet was received and the Xbox turned on. 3 attempts will be performed at turning the Xbox on. After the 3rd failed attempt, it will generate data for Alexa asking you to try again (It's worth noting that turning the Xbox on this way immediatly after you have turned it off doesn't always work. There is normally around 10-30 seconds of cool time before it will accept the packet).