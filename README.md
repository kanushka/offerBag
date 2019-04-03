# OfferBag - Visa Merchant Offers

This is a PHP web application for test **Visa APIs** with certificate files. This will retrieve Visa offers.

## install

Add your certificate (.pem), private key (.pem) files to `{app_root}/keys/visa/` folder and rename those to cert.pem and key.pem



## APIs

Get all offers

`offers`

Get selected offer details

`offers/{offer_id}`

Filter offers with available country

`offers/country/{country_code}`