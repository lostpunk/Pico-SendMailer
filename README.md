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
## Use
```html
<form id="send-order-form">
  <div class="form-row"><input type="text" id="name" name="name" value="" placeholder="name" required></div>
  <div class="form-row"><input type="tel" id="phone" name="phone" value="" placeholder="phone" required></div>
  <div class="form-row"><input type="email" id="mail" name="mail" value="" placeholder="mail"></div>
  <div class="form-row form-row--checkbox"><input type="checkbox" name="approve" id="approve" value="1" checked><label for="approve">approve <a href="">approve</a></label></div>
  <button type="submit" name="action" value="send-order" class="btn">Send</button>
  <div class="form-error"></div>
</form>
```
```js
$('body').on('submit', '#send-order-form', function (e) {
		e.preventDefault();
		$('.form-error').text('');
		var name = $('#name').val();
		var phone = $('#phone').val();
		var email = $('#mail').val();
		var approve = $('#approve').is(':checked') ? 1 : 0;
		$.ajax({
			url: '/send-mail',
			type: 'POST',
			data: {
				name: name,
				phone: phone,
				mail: email,
				approve: approve,
				action: 'send-order'
			},
			success: function (response) {
				if (response.status == 'OK') {
					alert("Success!");
				} else {
					$('.form-error').text(response.description);
				}
			},
			error: function () {
				alert("Error");
			}
		});
	})
```

## License
The MIT License (MIT)
