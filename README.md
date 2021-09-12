## 💼 &nbsp; About Runtastic_THT 1


 Create a RESTful stand alone web application that is able to persist collections of GPS points (collection in JSON format). These collections are called traces. You can download sample traces from here.

Keep in mind that this web application should work for a large amount of users and that it is likely to receive about 10 times more read than write requests.

### 🛠 &nbsp; Installation

This project was built with the popular Laravel Framework.

Follow the steps below to have it running on your local system:
Steps:
- Download and unzip the folder
- Change directory into the project root folder ```cd runtastic_test_one/```
- Run ```composer update``` to install al the required packages
- Setup your local ```MySQL``` database
- Add the database connection parameters to your ```.env``` file
- Run migrations ```php artisan migrate``` to create relevant tables
- Run ```php artisan key:generate``` to generate the secure application keys

If you encounter any problems installing the project or you want to learn more about installing Laravel, you can go to the [Laravel Installation Guide](https://laravel.com/docs/8.x/installation).

### ⚙️ &nbsp; Running and Testing

To get this project up and running on port ```3003```,
 type ```php artisan serve --port=3003``` and hit enter.
 
 Then test all the existing endpoints using ```curl```, [Postman](https://www.postman.com/), or [REST Client plugin](https://github.com/Huachao/vscode-restclient) if you are using [VSCode](https://code.visualstudio.com/)<img src="https://raw.githubusercontent.com/ABSphreak/ABSphreak/master/gifs/Hi.gif" width="20px" />

##### **Using Curl**
- ```$ curl -i -X PUT -d @/tmp/trace1.json http://localhost:3003/traces/1 ```

- ```$ curl -i -X POST -d @/tmp/trace1.json http://localhost:3003/traces```


##### **Using REST Client plugin**

This is a lot easier and more fun.

Go to the ```test.http``` file at the root of the project, and if you have the plugin installed, you will see the ```Send Request``` links above each of the sample request I created in the file for tests.

You can edit and click each of the requests to see the responses.
### Further Information

If you need to learn more about this project, feel free to contact me.

<p align="left">
<a href="https://www.tortyemmanuel.com/"><img alt="Website" src="https://img.shields.io/badge/Website-www.tortyemmanuel.com-blue?style=rounded-square&logo=google-chrome"></a>
<a href="https://www.linkedin.com/in/emmanuel-torty-60052153/"><img alt="LinkedIn" src="https://img.shields.io/badge/LinkedIn-Emmanuel%20Torty-blue?style=rounded-square&logo=linkedin"></a>
<a href="mailto:torty.emmanuel@gmail.com"><img alt="Email" src="https://img.shields.io/badge/Email-torty.emmanuel@gmail.com-blue?style=rounded-square&logo=gmail"></a>
</p>
