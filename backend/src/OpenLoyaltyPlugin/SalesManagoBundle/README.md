---
Bundle created to support communication with Salesmanago - based on Pixers sending package.

By default, configuration is loaded from either DemoBundle, or needs to be injected via API

Sample request to set proper credentials:
/api/plugin
Method: POST
type: application/json

{
	"salesManago":
	{
	      "salesManagoIsActive": true,
          "salesManagoApiEndpoint": "http://www.salesmanago.pl/api",
          "salesManagoApiSecret": "secret",
          "salesManagoApiKey": "key",
          "salesManagoCustomerId": "custid",
          "salesManagoOwnerEmail": "mail@mail.com."
	}
	
}

Important notes:
Plugin works as a bald sender, with no great way to support sending failed requests again. 
All informations about what happened to requests are stored in plugin.log, in var/logs. 

If request fails, it goes to deadletter table in database, as serialised object with repeat counter - later it will may be used in command, to send it one more time, 
for example if API falls. 

Right now there is no such command, yet it is simple. 


Due to Salesmanago and project specification, there is a need of translations - located in messages.pl.yml.


What needs to be done:

Definetely, when there will be enough time, there is a big need to move it to RabbitMQ. 