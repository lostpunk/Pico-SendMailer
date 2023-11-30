# Pico CMS SendMail plugin

## Requirements
- phpmailer/phpmailer

## Install
```sh
composer require phpmailer/phpmailer
```
- Copy `SendMail.php` to your `plugins` directory
- Copy & Paste the configuration array below in `config/config.yml or config.php`
- Сopy the file `send-mail.md` in the content directory
- Сopy the `empty.twig` file to your theme directory
- Сopy the `eml.twig` file to your theme directory
- Customize the `eml.twig` file at your discretion

  

## Settings
```yml

 # Pico CMS SendMail Configuration
 
smtp:
  smtp: true
  host: smtp.host
  username: login
  password: password
  port: 465
  security: ssl
  from_email: robot@test.test
  reply_to: robot@test.test
  from_name : robot
```


## License
The MIT License (MIT)
