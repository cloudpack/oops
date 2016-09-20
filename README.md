# Oops

Oops is testing tool for SOAP.


## install

Clone Project
```
git clone https://github.com/cloudpack/oops
```

Install PHP Packages
```
cd path/to/oops
php composer.phar install
```
To enter a blank to all parameters.
```
Some parameters are missing. Please provide them.
database_host (127.0.0.1):
database_port (null):
database_name (symfony):
database_user (root):
database_password (null):
mailer_transport (smtp):
mailer_host (127.0.0.1):
mailer_user (null):
mailer_password (null):
secret (ThisTokenIsNotSoSecretChangeIt):
```

Install Node Modules
```
npm install
```

Build JavaScript Source
```
webpack
```

Run Server
```
php bin/console server:run
```

## Usage

Access WebSite
```
http://127.0.0.1:8000
```

2. Upload `.wsdl` file.
3. Set parameters.  
   eg) SOAP version, headers, args...
4. When you click `request` button, then it will be sent.


## Notice
**Please have this app is to operate in the local environment .**  
