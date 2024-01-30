## Introduction

Our OpenCart plug-in comes with regular updates and full integration support, offering a flexible out-of-the-box solution to accept online payments easily.

Supports [Hosted Checkout Page](https://docs.direct.worldline-solutions.com/en/integration/basic-integration-methods/hosted-checkout-page) integration mode.

Offers the following payment methods on our platform:
- Alipay+
- American Express
- Apple Pay
- Bizum
- Diners Club
- Google Pay
- Meal vouchers
- Klarna
- Maestro
- MasterCard
- Oney 3x-4x
- PayPal
- Visa
- WeChat Pay

Accepts payment operations (refunds, authorizations, captures, etc.) directly from your Direct account.

## Install plugin
The first step to use the plugin is the installation process. Before you proceed, make sure your infrastructure meets these system requirements:

- Download the plugin from [GitHub](https://github.com/Dreamvention/worldline/releases) or from [OpenCart](https://github.com/Dreamvention/worldline/releases)
- Active [test](https://secure.ogone.com/Ncol/Test/Backoffice/login/)/[live](https://secure.ogone.com/Ncol/Prod/Backoffice/login/) account on our platform
- [API key/secret](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/authentication)
- [Webhooks](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/webhooks)

Once done, follow these steps:
1. Go to Extension -> Installer, press the "Upload" button, and upload the archive.
2. Go to Extensions -> Payments -> Worldline and click the "Install" button.

## Configure plugin
After the installation, you need to configure the plugin to link your store to our platform.

### Configure Basic settings
1. Log in to the OpenCart Back Office. Go to Extensions -> Payments -> Worldline > Account Settings tab to configure the following settings:

| Property                      | Description                                                                                                                                                                                                                                                                                                                                                           |
|-------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Status                        | Enabled/Disabled the payment module                                                                                                                                                                                                                                                                                                                                   |
| Environment                   | Select between "Test Mode” or "Live Mode” to link your shop to the respective [environment](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/environments) and to configure the respective test/live credentials. Depending on your selection, the shop module will send the transaction requests to the test or production environment |
| Test/Live Merchant ID (PSPID) | Enter your test/live PSPID from our platform that you want to use for transaction processing                                                                                                                                                                                                                                                                          |
| Test/Live API Key             | Enter the API Key of your test or live PSPID. Read our [dedicated guide](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/authentication) to learn how to generate one                                                                                                                                                                  |
| Test/Live API Secret          | Enter the API Secret of your test or live PSPID. Read our [dedicated guide](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/authentication) to learn how to generate one                                                                                                                                                               |
| Test/Live API Endpoint        | This field is filled in by default, but can be changed if you wish.                                                                                                                                                                                                                                                                                                   |
| Test/Live Webhooks Key        | Enter the Webhooks Key of your test/live PSPID from the Merchant Portal as described in [our dedicated guide](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/webhooks)                                                                                                                                                                |
| Test/Live Webhooks Secret     | Enter the Webhooks Secret of your test/live PSPID from the Merchant Portal as described in [our dedicated guide](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/webhooks)                                                                                                                                                             |
| Webhooks URL                  | Copy this URL into the Endpoint URLs fields in the Merchant Portal as described in [our dedicated guide](https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/webhooks)                                                                                                                                                                     |

2. Click on "Save” to confirm and validate your settings by establishing a test connection between the plugin and our platform. Check that the screen displays “Success: You have modified Worldline!”. If the text does not appear, contact your system administrator for troubleshooting to check
    - You are using the correct credentials
    - Whether your PSPID is active
    - You send the request to the correct PSPID/environment (Test vs Live)

### Configure Advanced settings
1. Log in to the OpenCart Back Office. Go to Extensions -> Payments -> Worldline > Advanced Settings tab to configure the following settings:

| Property                | Description                                                                                                                                                                                 |
|-------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Payment Title           | This feature enables you to modify the title of the payment method that will appear on the checkout page.                                                                                   |
| Payment Button Title    | This feature allows you to modify the title of the confirm button                                                                                                                           |
| Authorization Mode      | Three options are available: Pre Authorization, Final Authorization, Sale                                                                                                                   |
| Group Cards             | Allows you to group/ungroup all card payment methods under one single button on the Hosted Checkout Page                                                                                    |
| 3DS Challenge Indicator | Four options are available: No Preference, No Challenge Requested, Challenge Requested, Challenge Required                                                                                  |
| 3DS Exemption Request   | Five options are available: No exemption flagging, Automatic best possible exemption, Transaction is of low risk, The value of the transaction is below 30 EUR, Whitelisted by the customer |
| Debug Logging           | This option allows you to enable/disable event logging                                                                                                                                      |
| Total                   | The order amount must be reached before this payment method becomes active. Empty by default.                                                                                               |
| Geo Zone                | You have the option to choose specific geographical zones where the payment method will be accessible.                                                                                      |
| Sort Order              | This feature allows you to specify the order in which payment methods are displayed on the checkout page.                                                                                   |

2. Click on "Save" to confirm your settings.

## Manage payments
We have designed the plugin to follow up on your orders automatically and autonomously, freeing you from the administration involved. Learn here how to use our plugin effectively which could help your business to thrive!

### Perform Maintenance Operations
Captures and refunds of authorizations are standard processes (maintenance operations) in your everyday business logic. Learn here how to perform these operations directly in the OpenCart Back Office:
1. Log in to the OpenCart Back Office. Go to Extensions -> Payments -> Worldline. Click on the Transactions tab.
2. In the order overview, click on one of the available buttons to cancel/capture/refund the order. Depending on the order’s status, the following buttons are available

| Order status | Available buttons                                                                             |
|--------------|-----------------------------------------------------------------------------------------------|
| Completed    | "Worldline Capture Payment": Capture the authorized amount to receive the funds for the order |
|              | "Refund": Cancel the authorized amount (Due to the limitations of the platform, only a full refund is possible.)                                                       |
| Processing   | "Refund": Reimburse the funds for the order                                                   |

### Perform test transactions
Use our platform's test environment to make sure your plugin works as intended. We offer test data sets on our dedicated [Test cases](https://docs.direct.worldline-solutions.com/en/integration/how-to-integrate/test-cases/) page. Target our test environment as described in the "Configure Plugin" section.

> Make sure to switch to the LIVE environment as soon as you have finalised your tests
