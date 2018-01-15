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

Finally, you'll need to have [composer](https://getcomposer.org/download/), the common PHP package manager, available and then to run `composer install`. Completing this will bring in the dependent libraries to `site/vendor`.

IP Address & Xbox Live device ID
-----

For the app to connect to your Xbox, 3 things are required:

1. Your router must forward port 5050 to your Xbox.
1. As your Xbox will be turned on remotely, the public IP address for your Xbox is needed.
1. The Xbox Live device ID. On your Xbox: All settings > System > Console Info > Xbox Live device ID.

Setting up Alexa
-----

1. Setup an account/Log in at https://developer.amazon.com/home.html
1. Click on the [Alexa](https://developer.amazon.com/edw/home.html) tab
1. Click on _Alexa Skills Kit_
1. Click _Add a New Skill_
1. Select `Custom Skill` and your language settings
1. In the _Skill Information_ tab, enter _Name_: `Xbox Control` and _Invocation Name_: `xbox control` (lowercase)
1. Copy the _Application Id_ to your `.env` file
1. In the _Interaction Model_ tab enter the following as your _Intent Schema_:

    ```javascript
    {
      "intents": [
        {
          "intent": "XboxControl"
        }
      ]
    }
    ```

1. In the _Sample Utterances_ section enter (or other phrases you wish to use with the Intent Schema prefix):

    ```text
    XboxControl on
    XboxControl turn on
    XboxControl power on
    XboxControl start
    XboxControl switch on
    XboxControl to turn on
    XboxControl to power on
    XboxControl to start
    XboxControl to switch on
    ```

1. In the _Configuration_ tab, pick `https` and enter the URL of your hosted codebase. (If you don't have an SSL certificate, take a look at [EFF's Certbot](https://certbot.eff.org/) to set one up)
1. On the _Test_ tab, ensure the `Enabled` toggle is set to `This skill is enabled for testing on your account.` Further down on this tab, there's a useful _Service Simulator_ to allow you to test (and see the data passing back and forth).
1. The _Pubishing Information_ tab, you can add descriptions, example phrase and images to make things look as they should in your Alexa phone app. You **do not**, however, need to `Submit for Certification` for use for your own purposes - stay in test mode it will all work fine.
1. On the _Privacy & Compliance_ tab, check the `Export Compliance` statment.
1. All other settings, in tabs, can be left as defaults.
1. Click save and your Alexa skill will be ready to use, you can see it in your Alexa App under Skills > Your Skills > All Skills
1. To invoke Alexa to turn your Xbox on, try phrases like `Alexa, tell Xbox Control to turn on` or similar variants as you set up in _Sample Utterances_.


Testing
-----

It's probably a good idea to test the connection first, between the server that runs the app and your Xbox. To do this, you can simply set `.env` DEBUG to `true` so authentication requests from Alexa will be bypassed by this feature.

How it works
-----

The first thing the app does, is check that the Xbox is not already running by pinging it. If it isn't, then it will attempt to send a magic packet to the Xbox. It will wait one second before pinging again to see if the packet was received and the Xbox turned on. Five attempts will be performed at turning the Xbox on. After the 5th failed attempt, it will generate data for Alexa asking you to try again (It's worth noting that turning the Xbox on this way immediately after you have turned it off doesn't always work. There is normally around 10-30 seconds of cool-off time before it will accept the WOL packet).
